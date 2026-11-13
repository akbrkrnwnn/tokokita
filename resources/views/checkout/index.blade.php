@extends('layouts.app')

@section('title', 'Checkout - TokoKita')

@section('content')
    <div class="page-heading">
        <p class="eyebrow">Langkah terakhir</p>
        <h1 class="h2">Checkout</h1>
        <p>Isi alamat pengiriman dan periksa kembali ringkasan pesananmu.</p>
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

    <div class="row g-4 align-items-start">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h5 mb-4">Alamat pengiriman</h2>
                    <form action="{{ route('checkout.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Alamat lengkap</label>
                            <textarea name="shipping_address" id="shipping_address" class="form-control" rows="5" autocomplete="street-address" required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">Buat pesanan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-4">
                    <p class="eyebrow">Ringkasan pesanan</p>
                    <h2 class="h5 mb-3">{{ $cartItems->count() }} item</h2>
                    <ul class="list-group list-group-flush mb-3">
                        @foreach ($cartItems as $item)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">
                                <span>{{ $item->barang->nama }} <span class="text-muted">x{{ $item->qty }}</span></span>
                                <span class="text-nowrap">Rp {{ number_format($item->barang->harga * $item->qty, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="d-flex justify-content-between gap-3 border-top pt-3 mb-4 fw-bold">
                        <span>Total</span>
                        <span class="text-nowrap">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100">Kembali ke keranjang</a>
                </div>
            </div>
        </div>
    </div>
@endsection
