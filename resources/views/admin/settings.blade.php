@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
@php($s = $settings)
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="grid-2-equal">
        <div class="card">
            <div class="card-header"><h3>General</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Site name <span class="req">*</span></label>
                        <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $s['site_name']) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tagline (under logo)</label>
                        <input type="text" name="site_tagline" class="form-control" value="{{ old('site_tagline', $s['site_tagline']) }}">
                    </div>
                    <div class="form-group full">
                        <label>Footer slogan</label>
                        <input type="text" name="footer_text" class="form-control" value="{{ old('footer_text', $s['footer_text']) }}">
                    </div>
                    <div class="form-group full">
                        <label>Logo</label>
                        <div style="display:flex;gap:14px;align-items:center">
                            <div class="image-preview" id="logoPreview" style="width:80px;margin:0;border-radius:50%">
                                @if($s['logo'])<img src="{{ asset($s['logo']) }}" alt="">@else<i class="fa-regular fa-image"></i>@endif
                            </div>
                            <div style="flex:1">
                                <input type="file" name="logo" accept="image/*" class="form-control" data-preview="logoPreview">
                                @if($s['logo'])<label class="check" style="margin-top:8px"><input type="checkbox" name="remove_logo" value="1"> Remove logo (use default)</label>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Contact & social</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $s['phone']) }}">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $s['email']) }}">
                    </div>
                    <div class="form-group full">
                        <label>Shop address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $s['address']) }}">
                    </div>
                    <div class="form-group">
                        <label>Facebook page URL</label>
                        <input type="url" name="facebook" class="form-control" value="{{ old('facebook', $s['facebook']) }}">
                    </div>
                    <div class="form-group">
                        <label>WhatsApp number</label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $s['whatsapp']) }}" placeholder="01XXXXXXXXX">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-2-equal">
        <div class="card">
            <div class="card-header"><h3>Delivery & payment</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Delivery charge inside Dhaka (৳)</label>
                        <input type="number" step="0.01" min="0" name="shipping_inside_dhaka" class="form-control" value="{{ old('shipping_inside_dhaka', $s['shipping_inside_dhaka']) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Delivery charge outside Dhaka (৳)</label>
                        <input type="number" step="0.01" min="0" name="shipping_outside_dhaka" class="form-control" value="{{ old('shipping_outside_dhaka', $s['shipping_outside_dhaka']) }}" required>
                    </div>
                    <div class="form-group full">
                        <label>Online payment (shurjoPay: bKash, Nagad, Rocket, cards)</label>
                        <dl class="detail-list">
                            <div><dt>Mode</dt><dd>
                                @if($gateway->mode() === 'live')
                                    <span class="badge badge-active">Live</span>
                                @else
                                    <span class="badge badge-warning">Sandbox (test payments only)</span>
                                @endif
                            </dd></div>
                            <div><dt>Status</dt><dd>
                                @if($gateway->isConfigured())
                                    <span class="badge badge-active">Configured</span>
                                @else
                                    <span class="badge badge-danger">Missing credentials</span>
                                @endif
                            </dd></div>
                        </dl>
                        <div class="help-text">For security, the shurjoPay credentials live in the <code>.env</code> file (<code>SHURJOPAY_*</code>). Set <code>SHURJOPAY_MODE=live</code> once shurjoPay activates your live account.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Home page banner</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Banner image (optional)</label>
                        <div class="image-preview wide" id="bannerPreview">
                            @if($s['banner_image'])<img src="{{ asset($s['banner_image']) }}" alt="">@else<span>Using the built-in text banner</span>@endif
                        </div>
                        <input type="file" name="banner_image" accept="image/*" class="form-control" data-preview="bannerPreview">
                        @if($s['banner_image'])<label class="check" style="margin-top:8px"><input type="checkbox" name="remove_banner_image" value="1"> Remove banner image</label>@endif
                        <div class="help-text">Recommended 1400×250px. Without an image, the text below is shown.</div>
                    </div>
                    <div class="form-group">
                        <label>Banner title</label>
                        <input type="text" name="banner_title" class="form-control" value="{{ old('banner_title', $s['banner_title']) }}">
                    </div>
                    <div class="form-group">
                        <label>Banner badge</label>
                        <input type="text" name="banner_badge" class="form-control" value="{{ old('banner_badge', $s['banner_badge']) }}">
                    </div>
                    <div class="form-group full">
                        <label>Banner subtitle</label>
                        <input type="text" name="banner_subtitle" class="form-control" value="{{ old('banner_subtitle', $s['banner_subtitle']) }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save settings</button>
    </div>
</form>
@endsection
