@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<div class="status-tabs" style="margin-bottom:18px">
    <a href="{{ route('admin.orders.index', request()->except('status', 'page')) }}" class="{{ request('status') ? '' : 'active' }}">
        All <span>{{ $counts->sum() }}</span>
    </a>
    @foreach(\App\Models\Order::STATUSES as $key => $label)
        <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $key])) }}" class="{{ request('status') === $key ? 'active' : '' }}">
            {{ $label }} <span>{{ $counts[$key] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="filters">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="search" class="form-control" style="min-width:260px" placeholder="Order no, customer name or phone" value="{{ request('search') }}">
            <button class="btn btn-outline" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </form>
    </div>
    <div class="card-body p-0 table-wrap">
        <table class="table">
            <thead>
            <tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th class="text-right"></th></tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-600">{{ $order->order_number }}</a></td>
                    <td>{{ $order->customer_name }}<br><small class="text-muted">{{ $order->phone }}</small></td>
                    <td>{{ $order->items_count }}</td>
                    <td class="fw-600">{{ money($order->total) }}</td>
                    <td>
                        {{ $order->payment_method_label }}<br>
                        <span class="badge badge-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span>
                    </td>
                    <td><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></td>
                    <td class="text-muted">{{ $order->created_at->format('d M Y') }}<br><small>{{ $order->created_at->format('h:i A') }}</small></td>
                    <td><div class="actions"><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline btn-sm">View</a></div></td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>No orders found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->total())
        <div class="pagination-bar">
            <span>Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }}</span>
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
