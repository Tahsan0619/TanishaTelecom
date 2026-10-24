<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\ShurjoPay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn ($q) => $q->where('order_number', 'like', "%{$s}%")
                    ->orWhere('customer_name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = Order::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product', 'user');

        return view('admin.orders.show', compact('order'));
    }

    public function invoice(Order $order): View
    {
        $order->load('items');

        return view('admin.orders.invoice', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'payment_status' => ['required', Rule::in(array_keys(Order::PAYMENT_STATUSES))],
        ]);

        DB::transaction(function () use ($order, $data) {
            $wasCancelled = $order->status === 'cancelled';
            $isCancelled = $data['status'] === 'cancelled';

            // Return stock when an order is cancelled and take it again if it is re-opened.
            if ($wasCancelled !== $isCancelled) {
                foreach ($order->items as $item) {
                    $product = $item->product_id ? Product::lockForUpdate()->find($item->product_id) : null;
                    if (! $product) {
                        continue;
                    }
                    if ($isCancelled) {
                        $product->increment('stock', $item->quantity);
                        $product->decrement('sold_count', min($item->quantity, $product->sold_count));
                    } else {
                        $product->decrement('stock', min($item->quantity, $product->stock));
                        $product->increment('sold_count', $item->quantity);
                    }
                }
            }

            $order->update($data);
        });

        return back()->with('success', 'Order updated.');
    }

    /** Re-checks an online payment with shurjoPay (useful if the customer closed the browser before returning). */
    public function verifyPayment(Order $order, ShurjoPay $gateway): RedirectResponse
    {
        if (! $order->sp_order_id) {
            return back()->with('error', 'This order has no shurjoPay payment to verify.');
        }

        try {
            $record = $gateway->verify($order->sp_order_id);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if (! $record || $record['customer_order_id'] !== $order->order_number) {
            return back()->with('error', 'shurjoPay has no matching payment for this order.');
        }

        $outcome = $gateway->applyVerification($order, $record);
        $message = 'shurjoPay says: '.($record['sp_message'] ?? $outcome).' (code '.$record['sp_code'].').';

        return back()->with($outcome === 'paid' ? 'success' : 'error', $outcome === 'paid' ? 'Payment confirmed. '.$message : $message);
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }
}
