@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="filters">
            <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
            <select name="category" class="form-control">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="form-control">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Hidden</option>
                <option value="out_of_stock" @selected(request('status') === 'out_of_stock')>Out of stock</option>
            </select>
            <button class="btn btn-outline" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add product</a>
    </div>
    <div class="card-body p-0 table-wrap">
        <table class="table">
            <thead>
            <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Sold</th><th>Status</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td><img src="{{ $product->image_url ?? asset('assets/images/placeholder.jpg') }}" class="thumb" alt=""></td>
                    <td>
                        <div class="fw-600">{{ $product->name }}</div>
                        <small class="text-muted">{{ $product->unit }}</small>
                        @if($product->is_featured)<span class="badge badge-featured">Featured</span>@endif
                        @if($product->free_shipping)<span class="badge badge-featured">Free delivery</span>@endif
                    </td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>
                        @if($product->on_sale)
                            <div class="fw-600">{{ money($product->sale_price) }}</div>
                            <small class="text-muted"><s>{{ money($product->price) }}</s></small>
                        @else
                            <div class="fw-600">{{ money($product->price) }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $product->stock == 0 ? 'badge-danger' : ($product->stock <= 5 ? 'badge-warning' : 'badge-active') }}">{{ $product->stock }}</span>
                    </td>
                    <td>{{ $product->sold_count }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="badge badge-{{ $product->is_active ? 'active' : 'inactive' }}" style="border:0;cursor:pointer" title="Click to toggle">
                                {{ $product->is_active ? 'Active' : 'Hidden' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ url('product.html?slug='.$product->slug) }}" target="_blank" class="btn btn-outline btn-sm btn-icon" title="View"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline btn-sm btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm="Delete this product?">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-icon" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-box-open"></i><p>No products found.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($products->total())
        <div class="pagination-bar">
            <span>Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }}</span>
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
