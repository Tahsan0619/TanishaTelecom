@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="card" style="max-width:640px">
    <div class="card-header"><h3>Admin account</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Name <span class="req">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="form-group">
                <label>Email <span class="req">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <hr style="border:0;border-top:1px solid var(--border);margin:24px 0">
            <p class="text-muted mt-0">Leave the password fields empty to keep your current password.</p>
            <div class="form-group">
                <label>Current password</label>
                <input type="password" name="current_password" class="form-control" autocomplete="current-password">
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>New password</label>
                    <input type="password" name="password" class="form-control" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label>Confirm new password</label>
                    <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                </div>
            </div>
            <div class="form-actions" style="margin-top:20px">
                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Update profile</button>
            </div>
        </form>
    </div>
</div>
@endsection
