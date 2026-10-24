@extends('admin.layouts.app')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="filters no-print" style="justify-content:space-between; margin-bottom:18px">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> All orders</a>
    <div class="filters">
        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-outline"><i class="fa-solid fa-print"></i> Print invoice</a>
        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" data-confirm="Delete this order permanently?">
            @csrf @method('DELETE')
            <button class="btn btn-danger"><i class="fa-solid fa-trash"></i> Delete</button>
        </form>
    </div>
</div>

<div class="grid-2">
    <div>
        <div class="card">
            <div class="card-header">
                <h3>Items</h3>
                <span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span>
            </div>
            <div class="card-body p-0 table-wrap">
                <table class="table">
                    <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th class="text-right">Total</th></tr></thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px">
                                    <img src="{{ $item->product?->image_url ?? asset('assets/images/placeholder.jpg') }}" class="thumb" alt="">
                                    <div>
                                        <div class="fw-600">{{ $item->product_name }}</div>
                                        @unless($item->product)<small class="text-muted">Product deleted</small>@endunless
                                    </div>
                                </div>
                            </td>
                            <td>{{ money($item->price) }}</td>
                            <td>× {{ $item->quantity }}</td>
                            <td class="text-right fw-600">{{ money($item->total) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body" style="border-top:1px solid var(--border)">
                <dl class="detail-list totals" style="max-width:340px;margin-left:auto">
                    <div><dt>Subtotal</dt><dd>{{ money($order->subtotal) }}</dd></div>
                    <div><dt>Delivery ({{ $order->shipping_area_label }})</dt><dd>{{ money($order->shipping_cost) }}</dd></div>
                    @if($order->discount > 0)
                        <div><dt>Discount</dt><dd>-{{ money($order->discount) }}</dd></div>
                    @endif
                    <div class="grand"><dt>Total</dt><dd>{{ money($order->total) }}</dd></div>
                </dl>
            </div>
        </div>

        @if($order->notes)
            <div class="card">
                <div class="card-header"><h3>Customer note</h3></div>
                <div class="card-body"><div class="message-body">{{ $order->notes }}</div></div>
            </div>
        @endif
    </div>

    <div>
        <div class="card">
            <div class="card-header"><h3>Update order</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label>Order status</label>
                        <select name="status" class="form-control">
                            @foreach(\App\Models\Order::STATUSES as $key => $label)
                                <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="help-text">Cancelling an order returns its items to stock.</div>
                    </div>
                    <div class="form-group">
                        <label>Payment status</label>
                        <select name="payment_status" class="form-control">
                            @foreach(\App\Models\Order::PAYMENT_STATUSES as $key => $label)
                                <option value="{{ $key }}" @selected($order->payment_status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save changes</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Customer</h3></div>
            <div class="card-body">
                <dl class="detail-list">
                    <div><dt>Name</dt><dd>{{ $order->customer_name }}</dd></div>
                    <div><dt>Phone</dt><dd><a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></dd></div>
                    @if($order->email)<div><dt>Email</dt><dd>{{ $order->email }}</dd></div>@endif
                    <div><dt>Address</dt><dd>{{ $order->address }}@if($order->city), {{ $order->city }}@endif</dd></div>
                    <div><dt>Area</dt><dd>{{ $order->shipping_area_label }}</dd></div>
                    <div><dt>Account</dt><dd>
                        @if($order->user)
                            <a href="{{ route('admin.customers.show', $order->user) }}">{{ $order->user->email }}</a>
                        @else
                            Guest checkout
                        @endif
                    </dd></div>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Payment</h3></div>
            <div class="card-body">
                <dl class="detail-list">
                    <div><dt>Method</dt><dd>{{ $order->payment_method_label }}</dd></div>
                    @if($order->payment_channel)
                        <div><dt>Paid with</dt><dd>{{ $order->payment_channel }}</dd></div>
                    @endif
                    @if($order->transaction_id)
                        <div><dt>Transaction ID</dt><dd><code>{{ $order->transaction_id }}</code></dd></div>
                    @endif
                    @if($order->sp_order_id)
                        <div><dt>shurjoPay ID</dt><dd><code>{{ $order->sp_order_id }}</code></dd></div>
                    @endif
                    <div><dt>Status</dt><dd><span class="badge badge-{{ $order->payment_status }}">{{ $order->payment_status_label }}</span></dd></div>
                    @if($order->paid_at)
                        <div><dt>Paid at</dt><dd>{{ $order->paid_at->format('d M Y, h:i A') }}</dd></div>
                    @endif
                    <div><dt>Placed</dt><dd>{{ $order->created_at->format('d M Y, h:i A') }}</dd></div>
                </dl>
                @if($order->sp_order_id && $order->payment_status !== 'paid')
                    <form method="POST" action="{{ route('admin.orders.verify-payment', $order) }}" style="margin-top:14px">
                        @csrf
                        <button class="btn btn-outline btn-block" type="submit"><i class="fa-solid fa-rotate"></i> Verify payment with shurjoPay</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
