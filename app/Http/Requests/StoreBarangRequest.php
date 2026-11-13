<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'        => ['required', 'string', 'max:100'],
            'sku'         => ['required', 'max:30', 'unique:barangs'],
            'harga'       => ['required', 'integer', 'min:0'],
            'stok'        => ['required', 'integer', 'min:0'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
        ];
    }

    public function messages(): array
    {
        return [
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
        ];
    }

    public function attributes(): array
    {
        return [
            'nama'        => 'Nama Produk',
            'sku'         => 'SKU',
            'harga'       => 'Harga',
            'stok'        => 'Stok',
            'kategori_id' => 'Kategori',
        ];
    }
}
