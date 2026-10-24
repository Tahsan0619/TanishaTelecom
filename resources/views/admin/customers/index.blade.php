@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="filters">
            <input type="text" name="search" class="form-control" style="min-width:260px" placeholder="Name, email or phone" value="{{ request('search') }}">
            <button class="btn btn-outline" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </form>
        <span class="text-muted">{{ $customers->total() }} registered customers</span>
    </div>
    <div class="card-body p-0 table-wrap">
        <table class="table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Spent</th><th>Joined</th><th class="text-right"></th></tr></thead>
            <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td class="fw-600">{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td>{{ $customer->orders_count }}</td>
                    <td>{{ money($customer->orders_total ?? 0) }}</td>
                    <td class="text-muted">{{ $customer->created_at->format('d M Y') }}</td>
                    <td><div class="actions"><a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline btn-sm">View</a></div></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-users"></i><p>No customers yet.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
        <div class="pagination-bar"><span></span>{{ $customers->links() }}</div>
    @endif
</div>
@endsection
