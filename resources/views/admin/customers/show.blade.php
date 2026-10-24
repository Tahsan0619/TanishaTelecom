@extends('admin.layouts.app')

@section('title', $user->name)

@section('content')
<div class="grid-2">
    <div class="card">
        <div class="card-header"><h3>Orders ({{ $orders->count() }})</h3></div>
        <div class="card-body p-0 table-wrap">
            <table class="table">
                <thead><tr><th>Order</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-600">{{ $order->order_number }}</a></td>
                        <td>{{ $order->items_count }}</td>
                        <td>{{ money($order->total) }}</td>
                        <td><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></td>
                        <td class="text-muted">{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state"><p>This customer hasn't ordered yet.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Profile</h3></div>
        <div class="card-body">
            <dl class="detail-list">
                <div><dt>Name</dt><dd>{{ $user->name }}</dd></div>
                <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $user->phone ?? '-' }}</dd></div>
                <div><dt>Address</dt><dd>{{ $user->address ?? '-' }}</dd></div>
                <div><dt>City</dt><dd>{{ $user->city ?? '-' }}</dd></div>
                <div><dt>Joined</dt><dd>{{ $user->created_at->format('d M Y') }}</dd></div>
            </dl>
            <form method="POST" action="{{ route('admin.customers.destroy', $user) }}" data-confirm="Delete this customer account? Their orders will be kept." style="margin-top:18px">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-block"><i class="fa-solid fa-user-xmark"></i> Delete customer</button>
            </form>
        </div>
    </div>
</div>
@endsection
