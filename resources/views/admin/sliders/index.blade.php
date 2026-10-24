@extends('admin.layouts.app')

@section('title', 'Sliders')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>Home page slider</h3>
            <small class="text-muted">"Main" slides rotate in the large box; "Side" images stack on the right.</small>
        </div>
        <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add slide</a>
    </div>
    <div class="card-body p-0 table-wrap">
        <table class="table">
            <thead><tr><th>Image</th><th>Title</th><th>Position</th><th>Order</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
            @forelse($sliders as $slider)
                <tr>
                    <td><img src="{{ $slider->image_url }}" class="thumb" style="width:110px" alt=""></td>
                    <td>
                        <div class="fw-600">{{ $slider->title ?: '-' }}</div>
                        <small class="text-muted">{{ $slider->subtitle }}</small>
                    </td>
                    <td>{{ ucfirst($slider->position) }}</td>
                    <td>{{ $slider->sort_order }}</td>
                    <td><span class="badge badge-{{ $slider->is_active ? 'active' : 'inactive' }}">{{ $slider->is_active ? 'Active' : 'Hidden' }}</span></td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-outline btn-sm btn-icon"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" data-confirm="Delete this slide?">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-images"></i><p>No slides yet.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
