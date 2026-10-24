@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>All categories ({{ $categories->count() }})</h3>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add category</a>
    </div>
    <div class="card-body p-0 table-wrap">
        <table class="table">
            <thead>
            <tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Order</th><th>Status</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>
                        @if($category->image_url)
                            <img src="{{ $category->image_url }}" class="thumb" alt="">
                        @else
                            <div class="thumb" style="display:grid;place-items:center;color:#bbb"><i class="fa-solid fa-layer-group"></i></div>
                        @endif
                    </td>
                    <td class="fw-600">{{ $category->name }}</td>
                    <td class="text-muted">{{ $category->slug }}</td>
                    <td><a href="{{ route('admin.products.index', ['category' => $category->id]) }}">{{ $category->products_count }}</a></td>
                    <td>{{ $category->sort_order }}</td>
                    <td><span class="badge badge-{{ $category->is_active ? 'active' : 'inactive' }}">{{ $category->is_active ? 'Active' : 'Hidden' }}</span></td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="Delete this category?">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-icon" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-layer-group"></i><p>No categories yet.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
