<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * shurjoPay v2 REST integration (get_token → secret-pay → verification).
 *
 * @see https://www.shurjopay.com.bd/frontend-developers/direct-integration-with-api
 */
class ShurjoPay
{
    public const SUCCESS = '1000';

    public const CANCELLED = '1002';

    /** Codes that mean the customer has not finished paying yet. */
    private const IN_PROGRESS = ['1068'];

    private string $mode;

    private array $credentials;

    public function __construct()
    {
        $this->mode = config('services.shurjopay.mode') === 'live' ? 'live' : 'sandbox';
        $this->credentials = (array) config('services.shurjopay.'.$this->mode);
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function isConfigured(): bool
    {
        return filled($this->credentials['url'] ?? null)
            && filled($this->credentials['username'] ?? null)
            && filled($this->credentials['password'] ?? null);
    }

    /**
     * Starts a payment for the order and returns the hosted checkout URL.
     *
     * @return array{checkout_url: string, sp_order_id: string}
     */
    public function createPayment(Order $order, string $returnUrl, string $cancelUrl, ?string $clientIp = null): array
    {
        $data = $this->authorized('/api/secret-pay', fn (array $auth) => [
            'prefix' => config('services.shurjopay.prefix'),
            'token' => $auth['token'],
            'store_id' => $auth['store_id'],
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl,
            'amount' => round((float) $order->total, 2),
            'order_id' => $order->order_number,
            'currency' => 'BDT',
            'customer_name' => $order->customer_name,
            'customer_address' => $order->address,
            'customer_email' => $order->email ?: config('services.shurjopay.fallback_email'),
            'customer_phone' => $order->phone,
            'customer_city' => $order->city ?: 'Dhaka',
            'customer_post_code' => '1000',
            'customer_country' => 'Bangladesh',
            'client_ip' => $clientIp ?: '127.0.0.1',
            'value1' => $order->order_number,
        ]);

        if (empty($data['checkout_url']) || empty($data['sp_order_id'])) {
            report(new RuntimeException('shurjoPay secret-pay returned no checkout URL: '.json_encode($data)));
            throw new RuntimeException('Could not start the online payment. Please try again.');
        }

        return ['checkout_url' => $data['checkout_url'], 'sp_order_id' => $data['sp_order_id']];
    }

    /** Fetches the gateway's record for a shurjoPay order id (e.g. "TTM6abed2a6b3d6b"). */
    public function verify(string $spOrderId): ?array
    {
        $data = $this->authorized('/api/verification', fn () => ['order_id' => $spOrderId]);
        $record = array_is_list($data) ? ($data[0] ?? null) : $data;

        return is_array($record) && isset($record['sp_code'], $record['customer_order_id']) ? $record : null;
    }

    /**
     * Applies a verification record to the order.
     *
     * @return string paid | pending | cancelled | failed
     */
    public function applyVerification(Order $order, array $record): string
    {
        if ($order->payment_status === 'paid') {
            return 'paid';
        }

        $code = (string) ($record['sp_code'] ?? '');

        if ($code === self::SUCCESS) {
            if ((float) ($record['amount'] ?? 0) + 0.01 < (float) $order->total) {
                report(new RuntimeException("shurjoPay amount mismatch for {$order->order_number}: ".json_encode($record)));
                $order->update(['payment_status' => 'failed']);

                return 'failed';
            }

            $order->update([
                'payment_method' => 'shurjopay',
                'payment_status' => 'paid',
                'sp_order_id' => $record['order_id'] ?? $order->sp_order_id,
                'transaction_id' => ($record['bank_trx_id'] ?? null) ?: ($record['order_id'] ?? null),
                'payment_channel' => $record['method'] ?? null,
                'paid_at' => now(),
            ]);

            return 'paid';
        }

        if ($code === '' || in_array($code, self::IN_PROGRESS, true)) {
            return 'pending';
        }

        $order->update(['payment_status' => 'failed']);

        return $code === self::CANCELLED ? 'cancelled' : 'failed';
    }

    /** Sends an authenticated request, refreshing the cached token once if it was rejected. */
    private function authorized(string $path, callable $payload): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Online payment is not configured yet. Please choose Cash on Delivery.');
        }

        foreach ([false, true] as $isRetry) {
            $auth = $this->token($isRetry);
            $response = $this->send($path, $payload($auth), $auth['token']);

            if ($response->status() === 401 && ! $isRetry) {
                continue;
            }
            if ($response->successful() && is_array($response->json())) {
                return $response->json();
            }

            report(new RuntimeException("shurjoPay {$path} failed ({$response->status()}): ".$response->body()));
            break;
        }

        throw new RuntimeException('The payment gateway is not responding. Please try again in a moment.');
    }

    /** @return array{token: string, store_id: int|string} */
    private function token(bool $fresh = false): array
    {
        $key = 'shurjopay_token_'.$this->mode;
        if ($fresh) {
            Cache::forget($key);
        }

        // Tokens live for 15 minutes; refresh a little early.
        return Cache::remember($key, now()->addMinutes(12), function () {
            $response = $this->send('/api/get_token', [
                'username' => $this->credentials['username'],
                'password' => $this->credentials['password'],
            ]);
            $data = $response->json();

            if (! $response->successful() || empty($data['token'])) {
                report(new RuntimeException('shurjoPay get_token failed: '.$response->body()));
                throw new RuntimeException('Online payment is temporarily unavailable. Please try again later or choose Cash on Delivery.');
            }

            return ['token' => $data['token'], 'store_id' => $data['store_id']];
        });
    }

    private function send(string $path, array $body, ?string $token = null): Response
    {
        try {
            return $this->client($token)->post($path, $body);
        } catch (ConnectionException $e) {
            report($e);
            throw new RuntimeException('Could not connect to the payment gateway. Please try again.');
        }
    }

    private function client(?string $token): PendingRequest
    {
        $request = Http::baseUrl(rtrim($this->credentials['url'], '/'))
            ->acceptJson()
            ->asJson()
            ->timeout(30);

        return $token ? $request->withToken($token) : $request;
    }
}
