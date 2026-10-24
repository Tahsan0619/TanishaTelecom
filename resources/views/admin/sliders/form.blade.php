@extends('admin.layouts.app')

@section('title', $slider->exists ? 'Edit Slide' : 'Add Slide')

@section('content')
<form method="POST" enctype="multipart/form-data"
      action="{{ $slider->exists ? route('admin.sliders.update', $slider) : route('admin.sliders.store') }}">
    @csrf
    @if($slider->exists) @method('PUT') @endif

    <div class="grid-2">
        <div class="card">
            <div class="card-header"><h3>Slide details</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $slider->title) }}">
                    </div>
                    <div class="form-group full">
                        <label>Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $slider->subtitle) }}">
                    </div>
                    <div class="form-group full">
                        <label>Link (optional)</label>
                        <input type="text" name="link" class="form-control" value="{{ old('link', $slider->link) }}" placeholder="e.g. shop.html?category=yogurt">
                    </div>
                    <div class="form-group">
                        <label>Position <span class="req">*</span></label>
                        <select name="position" class="form-control">
                            @foreach(\App\Models\Slider::POSITIONS as $key => $label)
                                <option value="{{ $key }}" @selected(old('position', $slider->position) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sort order</label>
                        <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $slider->sort_order ?? 0) }}">
                    </div>
                    <div class="form-group full">
                        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $slider->is_active))> Active</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Image {!! $slider->exists ? '' : '<span class="req">*</span>' !!}</h3></div>
            <div class="card-body">
                <div class="image-preview wide" id="slidePreview">
                    @if($slider->image_url)
                        <img src="{{ $slider->image_url }}" alt="">
                    @else
                        <span><i class="fa-regular fa-image"></i> No image</span>
                    @endif
                </div>
                <input type="file" name="image" accept="image/*" class="form-control" data-preview="slidePreview" {{ $slider->exists ? '' : 'required' }}>
                <div class="help-text">Main: about 1200×700px. Side: about 500×400px.</div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline">Cancel</a>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save slide</button>
    </div>
</form>
@endsection
