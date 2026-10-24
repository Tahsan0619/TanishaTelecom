@extends('admin.layouts.app')

@section('title', $product->exists ? 'Edit Product' : 'Add Product')

@section('content')
<form method="POST" enctype="multipart/form-data"
      action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="grid-2">
        <div>
            <div class="card">
                <div class="card-header"><h3>Basic information</h3></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Product name <span class="req">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Slug (URL)</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $product->slug) }}" placeholder="e.g. galaxy-a55-8-128gb">
                            <div class="help-text">English letters, numbers and dashes. Leave empty to auto-generate.</div>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">None</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group full">
                            <label>Short description</label>
                            <textarea name="short_description" class="form-control" rows="3">{{ old('short_description', $product->short_description) }}</textarea>
                        </div>
                        <div class="form-group full">
                            <label>Full description</label>
                            <textarea name="description" class="form-control" rows="8">{{ old('description', $product->description) }}</textarea>
                            <div class="help-text">Basic HTML is allowed (e.g. &lt;b&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;p&gt;).</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Pricing & inventory</h3></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Regular price (৳) <span class="req">*</span></label>
                            <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Sale price (৳)</label>
                            <input type="number" step="0.01" min="0" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
                            <div class="help-text">Shows a "Sale!" badge when lower than the regular price.</div>
                        </div>
                        <div class="form-group">
                            <label>Stock quantity <span class="req">*</span></label>
                            <input type="number" min="0" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Variant</label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit) }}" placeholder="e.g. 8GB / 256GB, Midnight Black">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header"><h3>Product image</h3></div>
                <div class="card-body">
                    <div class="image-preview" id="productPreview">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="">
                        @else
                            <span><i class="fa-regular fa-image"></i> No image</span>
                        @endif
                    </div>
                    <input type="file" name="image" accept="image/*" class="form-control" data-preview="productPreview">
                    <div class="help-text">JPG, PNG or WEBP. Max 4 MB. Square images look best.</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Visibility</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Visible in shop</label>
                    </div>
                    <div class="form-group">
                        <label class="check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Featured product</label>
                        <div class="help-text">Featured products appear first on the home page.</div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="check"><input type="checkbox" name="free_shipping" value="1" @checked(old('free_shipping', $product->free_shipping))> Free delivery</label>
                        <div class="help-text">No delivery charge when every item in the order has free delivery.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Cancel</a>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save product</button>
    </div>
</form>
@endsection
