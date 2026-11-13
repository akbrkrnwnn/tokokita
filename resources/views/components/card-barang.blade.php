@props(['nama', 'harga', 'stok' => 0, 'kategori' => '-'])

<div class="col-md-4 mb-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">{{ $nama }}</h5>
            <small class="text-muted">{{ $kategori }}</small>
            <p class="card-text text-success">Rp {{ number_format($harga, 0, ',', '.') }}</p>
            <span class="badge {{ $stok > 0 ? 'bg-success' : 'bg-danger' }}">
                {{ $stok > 0 ? 'Stok: '.$stok : 'HABIS' }}
            </span>
        </div>
    </div>
</div>