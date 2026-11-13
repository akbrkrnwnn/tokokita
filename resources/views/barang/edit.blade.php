@extends('layouts.app')

@section('title', 'Edit Produk - TokoKita')

@section('content')
    <div class="page-heading">
        <p class="eyebrow">Kelola katalog</p>
        <h1 class="h2">Edit produk</h1>
        <p>Perbarui informasi <strong>{{ $barang->nama }}</strong>.</p>
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

    <form action="{{ route('barang.update', $barang->id) }}" method="POST" class="card form-panel p-4 p-md-5">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12">
                <label for="nama" class="form-label">Nama produk</label>
                <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $barang->nama) }}" required>
            </div>

            <div class="col-md-6">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku', $barang->sku) }}" required>
            </div>

            <div class="col-md-6">
                <label for="kategori_id" class="form-label">Kategori</label>
                <select name="kategori_id" id="kategori_id" class="form-select" required>
                    <option value="">Pilih kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id', $barang->kategori_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="harga" class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" id="harga" class="form-control" value="{{ old('harga', $barang->harga) }}" min="0" required>
            </div>

            <div class="col-md-6">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" name="stok" id="stok" class="form-control" value="{{ old('stok', $barang->stok) }}" min="0" required>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4">
            <a href="{{ route('katalog') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary px-4">Simpan perubahan</button>
        </div>
    </form>
@endsection
