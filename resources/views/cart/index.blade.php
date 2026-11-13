@extends('layouts.app')

@section('title', 'Keranjang Belanja - TokoKita')

@section('content')
    <div class="page-heading">
        <p class="eyebrow">Belanjaanmu</p>
        <h1 class="h2">Keranjang belanja</h1>
        <p>Periksa kembali produk dan jumlahnya sebelum membuat pesanan.</p>
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

    @if ($cartItems->isEmpty())
        <div class="empty-state">
            <h2 class="h5">Keranjangmu masih kosong</h2>
            <p>Jelajahi katalog dan tambahkan produk yang kamu suka.</p>
            <a href="{{ route('katalog') }}" class="btn btn-primary">Jelajahi katalog</a>
        </div>
    @else
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Produk</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col"><span class="visually-hidden">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('barang.show', $item->barang->id) }}" class="fw-semibold text-decoration-none">{{ $item->barang->nama }}</a>
                                        <div class="small text-muted">{{ $item->barang->kategori->nama ?? 'Tanpa kategori' }}</div>
                                    </td>
                                    <td class="text-nowrap">Rp {{ number_format($item->barang->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="qty" value="{{ $item->qty }}" min="1" class="form-control form-control-sm quantity-input" aria-label="Jumlah {{ $item->barang->nama }}">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Simpan</button>
                                        </form>
                                    </td>
                                    <td class="text-nowrap fw-semibold">Rp {{ number_format($item->barang->harga * $item->qty, 0, ',', '.') }}</td>
                                    <td>
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini dari keranjang?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('katalog') }}" class="btn btn-link px-0 mt-3 text-decoration-none">&larr; Lanjutkan belanja</a>
            </div>

            <div class="col-lg-4">
                <section class="card">
                    <div class="card-body p-4">
                        <p class="eyebrow">Ringkasan</p>
                        <h2 class="h5 mb-4">{{ $cartItems->count() }} item di keranjang</h2>
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span>Rp {{ number_format($cartItems->sum(fn($item) => $item->barang->harga * $item->qty), 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between gap-3 border-top pt-3 mb-4 fw-bold">
                            <span>Total</span>
                            <span class="text-nowrap">Rp {{ number_format($cartItems->sum(fn($item) => $item->barang->harga * $item->qty), 0, ',', '.') }}</span>
                        </div>
                        @if ($cartItems->sum(fn($item) => $item->barang->harga * $item->qty) > 0)
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">Lanjut ke checkout</a>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    @endif
@endsection
