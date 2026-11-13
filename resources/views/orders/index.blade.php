@extends('layouts.app')

@section('title', 'Pesanan Saya - TokoKita')

@section('content')
    <div class="page-heading">
        <p class="eyebrow">Aktivitas belanja</p>
        <h1 class="h2">Pesanan saya</h1>
        <p>Lacak status dan detail pesanan yang pernah kamu buat.</p>
    </div>

    @if (session('sukses'))
        <x-alert-status type="success" :message="session('sukses')" />
    @endif

    @if ($orders->isEmpty())
        <div class="empty-state">
            <h2 class="h5">Belum ada pesanan</h2>
            <p>Pesanan yang kamu buat akan tampil di halaman ini.</p>
            <a href="{{ route('katalog') }}" class="btn btn-primary">Mulai belanja</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Invoice</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Status</th>
                        <th scope="col">Total</th>
                        <th scope="col"><span class="visually-hidden">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->invoice_number }}</td>
                            <td class="text-nowrap">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="text-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Lihat detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
