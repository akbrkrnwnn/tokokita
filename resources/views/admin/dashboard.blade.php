@extends('layouts.app')

@section('title', 'Admin Panel - TokoKita')

@section('content')
    <div class="page-heading">
        <p class="eyebrow">Ruang pengelola</p>
        <h1 class="h2">Dashboard admin</h1>
        <p>Ringkasan katalog dan pintasan untuk mengelola TokoKita.</p>
    </div>

    <x-alert-status type="success" :message="session('sukses')" />

    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card">
                <p class="stat-label">Total produk</p>
                <p class="stat-value">{{ $totalBarang }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-warm">
                <p class="stat-label">Total kategori</p>
                <p class="stat-value">{{ $totalKategori }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-blue">
                <p class="stat-label">Admin aktif</p>
                <p class="stat-value">{{ Auth::user()->name }}</p>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 p-4">
            <div>
                <h2 class="h5 mb-1">Kelola toko</h2>
                <p class="text-muted mb-0">Atur produk atau lihat tampilan katalog publik.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('katalog') }}" class="btn btn-outline-primary">Lihat katalog</a>
                <a href="{{ route('barang.create') }}" class="btn btn-primary">Tambah produk</a>
            </div>
        </div>
    </div>
@endsection
