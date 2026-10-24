@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fa-solid fa-sack-dollar"></i></div>
        <div>
            <div class="label">Total Revenue</div>
            <div class="value">{{ money($stats['revenue']) }}</div>
            <div class="sub">Today: {{ money($stats['today_revenue']) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-bag-shopping"></i></div>
        <div>
            <div class="label">Total Orders</div>
            <div class="value">{{ number_format($stats['orders']) }}</div>
            <div class="sub"><a href="{{ route('admin.orders.index', ['status' => 'pending']) }}">{{ $stats['pending_orders'] }} pending</a></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-box"></i></div>
        <div>
            <div class="label">Products</div>
            <div class="value">{{ number_format($stats['products']) }}</div>
            <div class="sub {{ $stats['out_of_stock'] ? 'text-danger' : '' }}">{{ $stats['out_of_stock'] }} out of stock</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fa-solid fa-users"></i></div>
        <div>
            <div class="label">Customers</div>
            <div class="value">{{ number_format($stats['customers']) }}</div>
            <div class="sub"><a href="{{ route('admin.messages.index') }}">{{ $stats['unread_messages'] }} unread messages</a></div>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><h3>Sales: last 7 days</h3></div>
        <div class="card-body">
            <div class="bar-chart">
                @foreach($chart as $day)
                    <div class="bar-col">
                        <div class="bar" style="height: {{ max(1.5, $day['total'] / $chartMax * 85) }}%" title="{{ $day['count'] }} orders">
                            @if($day['total'] > 0)<span class="bar-tip">{{ money($day['total']) }}</span>@endif
                        </div>
                        <div class="bar-label">{{ $day['label'] }}<br>{{ $day['date'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Orders by status</h3></div>
        <div class="card-body">
            <dl class="detail-list">
                @foreach(\App\Models\Order::STATUSES as $key => $label)
                    <div>
                        <dt><span class="badge badge-{{ $key }}">{{ $label }}</span></dt>
                        <dd><a href="{{ route('admin.orders.index', ['status' => $key]) }}">{{ $statusCounts[$key] ?? 0 }}</a></dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h3>Recent orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline btn-sm">View all</a>
        </div>
        <div class="card-body p-0 table-wrap">
            <table class="table">
                <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-600">{{ $order->order_number }}</a></td>
                        <td>{{ $order->customer_name }}<br><small class="text-muted">{{ $order->phone }}</small></td>
                        <td>{{ money($order->total) }}</td>
                        <td><span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></td>
                        <td class="text-muted">{{ $order->created_at->format('d M, h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>No orders yet.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-header"><h3>Low stock</h3></div>
            <div class="card-body p-0 table-wrap">
                <table class="table">
                    <tbody>
                    @forelse($lowStock as $product)
                        <tr>
                            <td><a href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a></td>
                            <td class="text-right">
                                <span class="badge {{ $product->stock == 0 ? 'badge-danger' : 'badge-warning' }}">{{ $product->stock }} left</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-muted">All products are well stocked.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Best sellers</h3></div>
            <div class="card-body p-0 table-wrap">
                <table class="table">
                    <tbody>
                    @foreach($topProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td class="text-right text-muted">{{ $product->sold_count }} sold</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
