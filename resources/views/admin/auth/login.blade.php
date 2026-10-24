<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | {{ \App\Models\Setting::getValue('site_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('admin-assets/admin.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/icon.png') }}">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="brand">
            <img src="{{ asset('assets/images/icon.png') }}" alt="" class="brand-icon" style="object-fit:cover">
            <h1>{{ \App\Models\Setting::getValue('site_name') }}</h1>
            <p>Sign in to the admin panel</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa-solid fa-right-to-bracket"></i> Sign in</button>
        </form>
        <p style="text-align:center; margin-top:20px; font-size:13px">
            <a href="{{ url('/') }}"><i class="fa-solid fa-arrow-left"></i> Back to website</a>
        </p>
    </div>
</div>
</body>
</html>
