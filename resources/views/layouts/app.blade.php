<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#17253d">
    <title>@yield('title', 'TokoKita')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('katalog') }}">
                <span class="brand-mark">T</span>
                <span>TokoKita</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Buka navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavigation">
                <div class="navbar-nav me-auto">
                    <a href="{{ route('katalog') }}" class="nav-link {{ request()->routeIs('katalog', 'barang.show') ? 'active' : '' }}">Katalog</a>
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">Dashboard</a>
                        @endif
                        @if (auth()->user()->isBuyer())
                            <a href="{{ route('cart.index') }}" class="nav-link {{ request()->routeIs('cart.*', 'checkout.*') ? 'active' : '' }}">Keranjang</a>
                        @endif
                        @if (auth()->user()->isBuyer() || auth()->user()->isSeller())
                            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">Pesanan</a>
                        @endif
                        @if (auth()->user()->isAdmin() || auth()->user()->isSeller())
                            <a href="{{ route('barang.create') }}" class="nav-link {{ request()->routeIs('barang.create') ? 'active' : '' }}">Tambah produk</a>
                        @endif
                    @endauth
                </div>

                <div class="navbar-actions d-flex align-items-center gap-2">
                    @auth
                        <span class="account-label">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-nav-outline">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-nav-outline">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-nav-light">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="container app-main">
        @yield('content')
    </main>

    <footer class="app-footer">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <span class="footer-brand">TokoKita</span>
            <small>Belanja nyaman, pilihan lebih mudah.</small>
            <small>&copy; {{ date('Y') }} TokoKita</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
