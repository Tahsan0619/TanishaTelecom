@extends('admin.layouts.app')

@section('title', 'Pages')

@section('content')
<div class="card">
    <div class="card-header"><h3>Website pages</h3></div>
    <div class="card-body p-0 table-wrap">
        <table class="table">
            <thead><tr><th>Title</th><th>Page URL</th><th>Status</th><th>Last updated</th><th class="text-right"></th></tr></thead>
            <tbody>
            @foreach($pages as $page)
                <tr>
                    <td class="fw-600">{{ $page->title }}</td>
                    <td><a href="{{ url($page->slug.'.html') }}" target="_blank">/{{ $page->slug }}.html</a></td>
                    <td><span class="badge badge-{{ $page->is_active ? 'active' : 'inactive' }}">{{ $page->is_active ? 'Published' : 'Hidden' }}</span></td>
                    <td class="text-muted">{{ $page->updated_at->diffForHumans() }}</td>
                    <td><div class="actions"><a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-outline btn-sm"><i class="fa-solid fa-pen"></i> Edit</a></div></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
