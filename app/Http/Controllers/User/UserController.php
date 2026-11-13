<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('kategori')->get();

        return view('user.dashboard', compact('barangs'));
    }
}
