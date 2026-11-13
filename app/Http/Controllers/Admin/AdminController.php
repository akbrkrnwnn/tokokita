<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $totalKategori = \App\Models\Kategori::count();

        return view('admin.dashboard', compact('totalBarang', 'totalKategori'));
    }
}
