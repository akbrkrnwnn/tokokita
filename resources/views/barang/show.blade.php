@extends('layouts.app')

@section('title', $barang->nama.' - TokoKita')

@section('content')
    <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="eyebrow mb-1">Detail produk</p>
            <h1 class="h2 mb-0">{{ $barang->nama }}</h1>
        </div>
        <a href="{{ route('katalog') }}" class="btn btn-outline-secondary">Kembali ke katalog</a>
    </div>

    @if (session('sukses'))
        <x-alert-status type="success" :message="session('sukses')" />
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4 align-items-start">
        <div class="col-lg-7">
            <section class="card h-100">
                <div class="card-body p-4 p-md-5">
                    <span class="product-category">{{ $barang->kategori->nama ?? 'Tanpa kategori' }}</span>
                    <p class="text-muted mt-3 mb-4">Dijual oleh {{ $barang->seller->name ?? 'Seller TokoKita' }}</p>
                    <p class="detail-price mb-4">Rp {{ number_format($barang->harga, 0, ',', '.') }}</p>
                    <span class="badge rounded-pill {{ $barang->stok > 0 ? 'text-bg-success' : 'text-bg-danger' }}">
                        {{ $barang->stok > 0 ? $barang->stok.' tersedia' : 'Stok habis' }}
                    </span>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="card">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Pesan produk ini</h2>

                    @if (auth()->check() && auth()->user()->isBuyer())
                        @if ($barang->stok > 0)
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                                <div class="mb-3">
                                    <label for="qty" class="form-label">Jumlah</label>
                                    <input type="number" name="qty" id="qty" class="form-control" value="{{ old('qty', 1) }}" min="1" max="{{ $barang->stok }}" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Tambah ke keranjang</button>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">Produk ini sedang kehabisan stok.</div>
                        @endif
                    @elseif (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSeller()))
                        <p class="text-muted">Akun seller dan admin tidak dapat melakukan pembelian.</p>
                    @else
                        <p class="text-muted">Masuk untuk menambahkan produk ke keranjang dan melanjutkan pesanan.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">Masuk untuk belanja</a>
                    @endif

                    @if (auth()->check() && (auth()->user()->isAdmin() || (auth()->user()->isSeller() && $barang->seller_id === auth()->id())))
                        <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-outline-warning w-100 mt-3">Edit produk</a>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
