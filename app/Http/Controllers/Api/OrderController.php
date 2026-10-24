<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Services\ShurjoPay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(private readonly ShurjoPay $gateway) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:255'],
            'shipping_area' => ['required', Rule::in(array_keys(Order::SHIPPING_AREAS))],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'phone.regex' => 'Please enter a valid Bangladeshi mobile number (e.g. 01XXXXXXXXX).',
        ]);

        if ($data['payment_method'] === 'shurjopay' && ! $this->gateway->isConfigured()) {
            throw ValidationException::withMessages([
                'payment_method' => 'Online payment is not available right now. Please choose Cash on Delivery.',
            ]);
        }

        // Merge duplicate lines for the same product.
        $quantities = [];
        foreach ($data['items'] as $item) {
            $quantities[$item['product_id']] = ($quantities[$item['product_id']] ?? 0) + $item['quantity'];
        }

        $order = DB::transaction(function () use ($data, $quantities, $request) {
            $products = Product::active()
                ->whereIn('id', array_keys($quantities))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $errors = [];
            foreach ($quantities as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product) {
                    $errors['items'][] = 'One of the products in your cart is no longer available.';
                } elseif ($product->stock < $qty) {
                    $errors['items'][] = $product->stock > 0
                        ? "Only {$product->stock} item(s) of \"{$product->name}\" are in stock."
                        : "\"{$product->name}\" is out of stock.";
                }
            }
            if ($errors) {
                throw ValidationException::withMessages($errors);
            }

            $subtotal = 0;
            $lines = [];
            foreach ($quantities as $productId => $qty) {
                $product = $products->get($productId);
                $lineTotal = $product->final_price * $qty;
                $subtotal += $lineTotal;
                $lines[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->final_price,
                    'quantity' => $qty,
                    'total' => $lineTotal,
                ];

                $product->decrement('stock', $qty);
                $product->increment('sold_count', $qty);
            }

            $shipping = $products->every->free_shipping ? 0.0 : (float) Setting::getValue(
                $data['shipping_area'] === 'outside_dhaka' ? 'shipping_outside_dhaka' : 'shipping_inside_dhaka',
                0
            );

            $order = Order::create([
                'order_number' => Order::generateNumber(),
                'user_id' => $request->user('sanctum')?->id,
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'] ?? null,
                'shipping_area' => $data['shipping_area'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'discount' => 0,
                'total' => $subtotal + $shipping,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'status' => 'pending',
            ]);

            $order->items()->createMany($lines);

            return $order;
        });

        $paymentUrl = null;
        if ($order->payment_method === 'shurjopay') {
            try {
                $paymentUrl = $this->startPayment($order, $request);
            } catch (RuntimeException $e) {
                // Don't keep an unpaid order (and reserved stock) the customer never got to pay for.
                $order->releaseStock();
                $order->delete();

                return response()->json(['message' => $e->getMessage()], 502);
            }
        }

        return response()->json([
            'message' => 'Your order has been placed successfully.',
            'data' => $order->load('items.product')->toApi(),
            'payment_url' => $paymentUrl,
        ], 201);
    }

    /** Starts a new shurjoPay payment for an existing unpaid order (e.g. after a failed attempt). */
    public function pay(Request $request, string $orderNumber): JsonResponse
    {
        $data = $request->validate(['phone' => ['required', 'string', 'max:30']]);

        $order = Order::where('order_number', $orderNumber)->where('phone', trim($data['phone']))->first();
        if (! $order) {
            return response()->json(['message' => 'No order found with this order number and phone.'], 404);
        }
        if (! $order->canPayOnline()) {
            return response()->json(['message' => 'This order cannot be paid online.'], 422);
        }

        try {
            return response()->json(['payment_url' => $this->startPayment($order, $request)]);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }
    }

    private function startPayment(Order $order, Request $request): string
    {
        $payment = $this->gateway->createPayment(
            $order,
            route('payment.shurjopay.return'),
            route('payment.shurjopay.cancel'),
            $request->ip()
        );

        $order->update(['sp_order_id' => $payment['sp_order_id']]);

        return $payment['checkout_url'];
    }

    public function track(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $order = Order::with('items.product')
            ->where('order_number', trim($data['order_number']))
            ->where('phone', trim($data['phone']))
            ->first();

        if (! $order) {
            return response()->json(['message' => 'No order found with this order number and phone.'], 404);
        }

        return response()->json(['data' => $order->toApi()]);
    }

    public function myOrders(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with('items.product')
            ->latest()
            ->get()
            ->map(fn (Order $o) => $o->toApi());

        return response()->json(['data' => $orders]);
    }

    public function myOrder(Request $request, string $orderNumber): JsonResponse
    {
        $order = $request->user()->orders()
            ->with('items.product')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json(['data' => $order->toApi()]);
    }
}
