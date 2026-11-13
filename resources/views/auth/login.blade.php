@extends('layouts.app')

@section('title', 'Login - TokoKita')

@section('content')
    <section class="card auth-panel">
        <div class="auth-heading">
            <p class="eyebrow">Selamat datang kembali</p>
            <h1 class="h2">Masuk ke TokoKita</h1>
            <p>Lanjutkan belanja dan kelola pesananmu.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">Masuk</button>
        </form>

        <p class="text-center text-muted mt-4 mb-0">
            Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold">Daftar di sini</a>
        </p>
    </section>
@endsection
