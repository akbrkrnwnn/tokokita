<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::table('barangs')->insert([
            ['nama' => 'Kopi Arabika', 'sku' => 'KM-001', 'harga' => 75000, 'stok' => 12, 'kategori_id' => 1],
            ['nama' => 'Teh Melati', 'sku' => 'KM-002', 'harga' => 25000, 'stok' => 0, 'kategori_id' => 1],
            ['nama' => 'Gula Pasir 1kg', 'sku' => 'BP-001', 'harga' => 18000, 'stok' => 30, 'kategori_id' => 3],
            ['nama' => 'Minyak Goreng 2L', 'sku' => 'BP-002', 'harga' => 34000, 'stok' => 20, 'kategori_id' => 3],
            ['nama' => 'Biskuit Cokelat', 'sku' => 'MK-001', 'harga' => 12000, 'stok' => 45, 'kategori_id' => 2],
        ]);
    }
}
