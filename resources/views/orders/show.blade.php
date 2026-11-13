@extends('layouts.app')

@section('title', 'Pesanan '.$order->invoice_number.' - TokoKita')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-3 page-heading">
        <div>
            <p class="eyebrow">Rincian transaksi</p>
            <h1 class="h2 mb-1">Detail pesanan</h1>
            <p class="mb-0">Invoice {{ $order->invoice_number }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Kembali ke pesanan</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <section class="card h-100">
                <div class="card-body p-4">
                    <p class="eyebrow">Informasi pesanan</p>
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted fw-normal">Invoice</dt>
                        <dd class="col-sm-8 fw-semibold">{{ $order->invoice_number }}</dd>
                        <dt class="col-sm-4 text-muted fw-normal">Tanggal</dt>
                        <dd class="col-sm-8">{{ $order->created_at->format('d M Y, H:i') }}</dd>
                        <dt class="col-sm-4 text-muted fw-normal">Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </section>
        </div>
        <div class="col-md-6">
            <section class="card h-100">
                <div class="card-body p-4">
                    <p class="eyebrow">Pengiriman</p>
                    <h2 class="h6">Alamat tujuan</h2>
                    <p class="mb-0 text-muted">{{ $order->shipping_address }}</p>
                </div>
            </section>
        </div>
    </div>

    <section class="card">
        <div class="card-body p-0">
            <div class="p-4 pb-3">
                <p class="eyebrow mb-1">Daftar belanja</p>
                <h2 class="h5 mb-0">Item pesanan</h2>
            </div>
            <div class="table-responsive border-0 rounded-0">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Produk</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Harga satuan</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->barang->nama }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">Total</td>
                        <td class="text-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </section>

    @if (Auth::user()->isAdmin() || Auth::user()->isSeller())
        <section class="card mt-4">
            <div class="card-body p-4">
                <h2 class="h5">Perbarui status</h2>
            <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <div class="d-flex flex-column flex-sm-row gap-2">
                <select name="status" class="form-select" aria-label="Status pesanan">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-primary text-nowrap">Simpan status</button>
                </div>
            </form>
            </div>
        </section>
    @endif
@endsection
