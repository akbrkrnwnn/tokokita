<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarangRequest;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('kategori', 'seller')->get();

        return view('barang.index', compact('barangs'));
    }

    public function show(Barang $barang)
    {
        $barang->load('kategori', 'seller');

        return view('barang.show', compact('barang'));
    }

    public function create()
    {
        if (!Auth::user()->isSeller() && !Auth::user()->isAdmin()) {
            abort(403, 'Hanya seller dan admin yang dapat menambah produk.');
        }

        $kategoris = Kategori::all();

        return view('barang.create', compact('kategoris'));
    }

    public function store(StoreBarangRequest $request)
    {
        if (!Auth::user()->isSeller() && !Auth::user()->isAdmin()) {
            abort(403, 'Hanya seller dan admin yang dapat menambah produk.');
        }

        $data = $request->validated();
        $data['seller_id'] = Auth::id();

        Barang::create($data);

        return redirect()->route('katalog')->with('sukses', 'Produk berhasil ditambahkan!');
    }

    public function edit(Barang $barang)
    {
        $this->authorizeSellerOrAdmin($barang);

        $kategoris = Kategori::all();

        return view('barang.edit', compact('barang', 'kategoris'));
    }

    public function update(Request $request, Barang $barang)
    {
        $this->authorizeSellerOrAdmin($barang);

        $data = $request->validate([
            'nama'        => ['required', 'string', 'max:100'],
            'sku'         => ['required', 'max:30', Rule::unique('barangs')->ignore($barang->id)],
            'harga'       => ['required', 'integer', 'min:0'],
            'stok'        => ['required', 'integer', 'min:0'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
        ], [
            'nama.required'            => 'Nama produk wajib diisi.',
            'nama.string'              => 'Nama produk harus berupa teks.',
            'nama.max'                 => 'Nama produk maksimal 100 karakter.',
            'sku.required'             => 'SKU wajib diisi.',
            'sku.max'                  => 'SKU maksimal 30 karakter.',
            'sku.unique'               => 'SKU ini sudah dipakai produk lain.',
            'harga.required'           => 'Harga wajib diisi.',
            'harga.integer'            => 'Harga harus berupa angka bulat.',
            'harga.min'                => 'Harga tidak boleh negatif.',
            'stok.required'            => 'Stok wajib diisi.',
            'stok.integer'             => 'Stok harus berupa angka bulat.',
            'stok.min'                 => 'Stok tidak boleh negatif.',
            'kategori_id.required'     => 'Kategori wajib dipilih.',
            'kategori_id.exists'       => 'Kategori yang dipilih tidak tersedia.',
        ], [
            'nama'        => 'Nama Produk',
            'sku'         => 'SKU',
            'harga'       => 'Harga',
            'stok'        => 'Stok',
            'kategori_id' => 'Kategori',
        ]);

        $barang->update($data);

        return redirect()->route('barang.index')->with('sukses', 'Produk berhasil diperbarui!');
    }

    public function destroy(Barang $barang)
    {
        $this->authorizeSellerOrAdmin($barang);

        $barang->delete();

        return redirect()->route('barang.index')->with('sukses', 'Produk berhasil dihapus!');
    }

    private function authorizeSellerOrAdmin(Barang $barang): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if (!$user->isSeller() || $barang->seller_id !== $user->id) {
            abort(403, 'Anda hanya dapat mengedit produk milik Anda sendiri.');
        }
    }
}
