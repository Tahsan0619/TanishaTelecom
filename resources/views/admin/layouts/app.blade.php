<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ \App\Models\Setting::getValue('site_name') }} Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('admin-assets/admin.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/icon.png') }}">
</head>
<body>
@php
    $unreadMessages = \App\Models\ContactMessage::where('is_read', false)->count();
    $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
@endphp
<div class="admin-wrapper">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('assets/images/icon.png') }}" alt="" class="brand-icon" style="object-fit:cover">
            <span>{{ \App\Models\Setting::getValue('site_name') }}<small>Admin Panel</small></span>
        </a>
        <nav>
            <div class="nav-label">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bag-shopping"></i> Orders
                @if($pendingOrders)<span class="badge-count">{{ $pendingOrders }}</span>@endif
            </a>

            <div class="nav-label">Catalog</div>
            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-box"></i> Products
            </a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Categories
            </a>

            <div class="nav-label">People</div>
            <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Customers
            </a>
            <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope"></i> Messages
                @if($unreadMessages)<span class="badge-count">{{ $unreadMessages }}</span>@endif
            </a>

            <div class="nav-label">Website</div>
            <a href="{{ route('admin.sliders.index') }}" class="{{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                <i class="fa-solid fa-images"></i> Sliders
            </a>
            <a href="{{ route('admin.pages.index') }}" class="{{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i> Pages
            </a>
            <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Settings
            </a>
            <a href="{{ url('/') }}" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Website
            </a>
        </nav>
    </aside>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu"><i class="fa-solid fa-bars"></i></button>
                <h1>@yield('title', 'Dashboard')</h1>
            </div>
            <div class="topbar-right">
                <a href="{{ route('admin.profile.edit') }}" class="topbar-user" title="My profile">
                    <span class="avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="name" style="color: var(--text)">{{ auth()->user()->name }}</span>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="btn btn-outline btn-sm" type="submit"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    document.getElementById('menuToggle').addEventListener('click', () => {
        sidebar.classList.toggle('open');
        backdrop.classList.toggle('open');
    });
    backdrop.addEventListener('click', () => {
        sidebar.classList.remove('open');
        backdrop.classList.remove('open');
    });

    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', e => {
            if (!confirm(form.dataset.confirm)) e.preventDefault();
        });
    });

    document.querySelectorAll('input[type=file][data-preview]').forEach(input => {
        input.addEventListener('change', () => {
            const box = document.getElementById(input.dataset.preview);
            const file = input.files[0];
            if (box && file) {
                box.innerHTML = '';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                box.appendChild(img);
            }
        });
    });
</script>
@stack('scripts')
</body>
</html>
