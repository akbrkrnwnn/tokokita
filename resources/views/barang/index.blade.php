@extends('layouts.app')

@section('title', 'Katalog Produk - TokoKita')

@section('content')
    <section class="catalog-hero">
        <div class="row align-items-end g-4">
            <div class="col-lg-8">
                <p class="eyebrow">Belanja di TokoKita</p>
                <h1>Temukan produk yang tepat untukmu.</h1>
                <p>Jelajahi pilihan produk dari seller lokal, cek ketersediaan stok, lalu lanjutkan belanja dengan mudah.</p>
            </div>
            @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSeller()))
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('barang.create') }}" class="btn btn-primary btn-lg">Tambah produk</a>
                </div>
            @endif
        </div>
    </section>

    @if (session('sukses'))
        <x-alert-status type="success" :message="session('sukses')" />
    @endif

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-2 mb-3">
        <div>
            <p class="eyebrow mb-1">Pilihan untukmu</p>
            <h2 class="h3 mb-0">Katalog produk</h2>
        </div>
        <span class="catalog-meta">{{ count($barangs) }} produk tersedia</span>
    </div>

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
                        <h3 class="product-title">
                            <a href="{{ route('barang.show', $b->id) }}">{{ $b->nama }}</a>
                        </h3>
                        <p class="product-price">Rp {{ number_format($b->harga, 0, ',', '.') }}</p>
                        <div class="mt-auto">
                            <a href="{{ route('barang.show', $b->id) }}" class="btn btn-outline-primary w-100">Lihat detail</a>
                        </div>
                    </div>
                    @if (auth()->check() && (auth()->user()->isAdmin() || (auth()->user()->isSeller() && auth()->id() === $b->seller_id)))
                        <div class="card-footer d-flex gap-2">
                            <a href="{{ route('barang.edit', $b->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                            <form action="{{ route('barang.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    @endif
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <h3 class="h5">Belum ada produk</h3>
                    <p>Produk yang tersedia akan muncul di sini.</p>
                    @if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSeller()))
                        <a href="{{ route('barang.create') }}" class="btn btn-primary">Tambah produk pertama</a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>
@endsection
