@extends('admin.layouts.app')

@section('title', 'Edit: '.$page->title)

@section('content')
<form method="POST" action="{{ route('admin.pages.update', $page) }}">
    @csrf @method('PUT')
    <div class="grid-2">
        <div class="card">
            <div class="card-header"><h3>Content</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Title <span class="req">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                </div>
                <div class="form-group mb-0">
                    <label>Content (HTML)</label>
                    <textarea name="content" id="pageContent" class="form-control code">{{ old('content', $page->content) }}</textarea>
                    <div class="help-text">Use HTML tags like &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;&lt;li&gt;, &lt;strong&gt;, &lt;img src="..."&gt;.</div>
                </div>
            </div>
        </div>
        <div>
            <div class="card">
                <div class="card-header"><h3>Publish</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active))> Published</label>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save page</button>
                    <a href="{{ url($page->slug.'.html') }}" target="_blank" class="btn btn-outline btn-block" style="margin-top:10px"><i class="fa-solid fa-eye"></i> View page</a>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Preview</h3></div>
                <div class="card-body" id="pagePreview" style="max-height:460px;overflow:auto;font-family:'Hind Siliguri',sans-serif;line-height:1.7"></div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const area = document.getElementById('pageContent');
    const preview = document.getElementById('pagePreview');
    const render = () => { preview.innerHTML = area.value; };
    area.addEventListener('input', render);
    render();
</script>
@endpush
