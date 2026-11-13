@extends('layouts.app')

@section('title', 'Daftar - TokoKita')

@section('content')
    <section class="card auth-panel">
        <div class="auth-heading">
            <p class="eyebrow">Bergabung bersama kami</p>
            <h1 class="h2">Buat akun TokoKita</h1>
            <p>Daftar untuk mulai menikmati pengalaman belanja yang mudah.</p>
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

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nama lengkap</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" autocomplete="name" required autofocus>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" autocomplete="email" required>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" autocomplete="new-password" required>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Konfirmasi password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password" required>
                @error('password_confirmation')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">Buat akun</button>
        </form>

        <p class="text-center text-muted mt-4 mb-0">
            Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold">Masuk di sini</a>
        </p>
    </section>
@endsection
