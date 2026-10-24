@extends('admin.layouts.app')

@section('title', $category->exists ? 'Edit Category' : 'Add Category')

@section('content')
<form method="POST" enctype="multipart/form-data"
      action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf
    @if($category->exists) @method('PUT') @endif

    <div class="grid-2">
        <div class="card">
            <div class="card-header"><h3>Category details</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Name <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Slug (URL)</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug) }}" placeholder="e.g. yogurt">
                        <div class="help-text">Leave empty to generate automatically. Use English letters for nice URLs.</div>
                    </div>
                    <div class="form-group full">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Sort order</label>
                        <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                    </div>
                    <div class="form-group" style="align-self:end">
                        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))> Visible in shop</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Image</h3></div>
            <div class="card-body">
                <div class="image-preview" id="catPreview">
                    @if($category->image_url)
                        <img src="{{ $category->image_url }}" alt="">
                    @else
                        <span><i class="fa-regular fa-image"></i> No image</span>
                    @endif
                </div>
                <input type="file" name="image" accept="image/*" class="form-control" data-preview="catPreview">
                @if($category->image)
                    <label class="check" style="margin-top:10px"><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
                @endif
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save category</button>
    </div>
</form>
@endsection
