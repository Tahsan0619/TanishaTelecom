<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ShurjoPay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PaymentController extends Controller
{
    public function __construct(private readonly ShurjoPay $gateway) {}

    /** shurjoPay sends the customer here after paying (or failing) with ?order_id=<sp_order_id>. */
    public function shurjopayReturn(Request $request): RedirectResponse
    {
        return $this->handle($request, false);
    }

    public function shurjopayCancel(Request $request): RedirectResponse
    {
        return $this->handle($request, true);
    }

    /** Server-to-server notification from shurjoPay; the order is only updated from the gateway's verified record. */
    public function shurjopayIpn(Request $request): JsonResponse
    {
        $spOrderId = (string) $request->input('order_id', $request->input('sp_order_id', ''));
        if ($spOrderId === '') {
            return response()->json(['status' => 'error', 'message' => 'order_id is required'], 422);
        }

        try {
            $record = $this->gateway->verify($spOrderId);
        } catch (RuntimeException $e) {
            return response()->json(['status' => 'error', 'message' => 'Verification failed'], 503);
        }

        $order = $record ? Order::where('order_number', $record['customer_order_id'])->first() : null;
        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'status' => 'ok',
            'order' => $order->order_number,
            'payment' => $this->gateway->applyVerification($order, $record),
        ]);
    }

    private function handle(Request $request, bool $cancelled): RedirectResponse
    {
        $spOrderId = (string) $request->input('order_id', '');
        if ($spOrderId === '') {
            return redirect('track-order.html?payment=failed');
        }

        try {
            $record = $this->gateway->verify($spOrderId);
        } catch (RuntimeException $e) {
            $record = null;
        }

        // Look the order up from the gateway's own record so a tampered URL can't mark another order as paid.
        $order = $record ? Order::where('order_number', $record['customer_order_id'])->first() : null;
        if (! $order) {
            $order = Order::where('sp_order_id', $spOrderId)->first();
            if (! $order) {
                return redirect('track-order.html?payment=failed');
            }

            return $this->toTracking($order, 'pending');
        }

        $outcome = $this->gateway->applyVerification($order, $record);
        if ($cancelled && $outcome === 'pending') {
            $outcome = 'cancelled';
        }

        return $this->toTracking($order, $outcome);
    }

    private function toTracking(Order $order, string $outcome): RedirectResponse
    {
        return redirect('track-order.html?'.http_build_query([
            'order' => $order->order_number,
            'phone' => $order->phone,
            'payment' => $outcome,
            'new' => 1,
        ]));
    }
}
