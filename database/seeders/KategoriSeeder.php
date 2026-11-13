<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::table('kategoris')->insert([
            ['nama' => 'Minuman', 'deskripsi' => 'Kopi, teh, dan minuman lain'],
            ['nama' => 'Makanan', 'deskripsi' => 'Makanan kering & instan'],
            ['nama' => 'Bahan Pokok', 'deskripsi' => 'Gula, beras, dan kebutuhan dapur'],
        ]);
    }
}
