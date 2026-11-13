@extends('layouts.app')

@section('title', 'User Panel - TokoKita')

@section('content')
    <div class="page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div>
            <p class="eyebrow">Area pelanggan</p>
            <h1 class="h2">Halo, {{ Auth::user()->name }}!</h1>
            <p>Temukan produk pilihan dan lanjutkan belanja kapan saja.</p>
        </div>
        @if (Auth::user()->isBuyer())
            <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">Lihat pesanan saya</a>
        @endif
    </div>

    <x-alert-status type="success" :message="session('sukses')" />

    <div class="row g-4">
        @forelse($barangs as $b)
            <div class="col-sm-6 col-xl-4">
                <article class="card product-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <span class="product-category">{{ $b->kategori->nama ?? 'Tanpa kategori' }}</span>
                            <span class="badge rounded-pill {{ $b->stok > 0 ? 'text-bg-success' : 'text-bg-danger' }}">
                                {{ $b->stok > 0 ? 'Stok: '.$b->stok : 'Habis' }}
                            </span>
                        </div>
                        <h2 class="product-title"><a href="{{ route('barang.show', $b->id) }}">{{ $b->nama }}</a></h2>
                        <p class="product-price">Rp {{ number_format($b->harga, 0, ',', '.') }}</p>
                        <a href="{{ route('barang.show', $b->id) }}" class="btn btn-outline-primary w-100 mt-auto">Lihat detail</a>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <h2 class="h5">Belum ada produk</h2>
                    <p>Silakan kembali lagi nanti untuk melihat produk yang tersedia.</p>
                    <a href="{{ route('katalog') }}" class="btn btn-outline-primary">Jelajahi katalog</a>
                </div>
            </div>
        @endforelse
    </div>
@endsection
