<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('barang.kategori')->where('user_id', Auth::id())->get();

        return view('cart.index', compact('cartItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $barang = Barang::find($request->barang_id);

        if ($barang->stok < $request->qty) {
            return back()->withErrors(['qty' => 'Stok tidak mencukupi.']);
        }

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('barang_id', $request->barang_id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('qty', $request->qty);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'barang_id' => $request->barang_id,
                'qty' => $request->qty,
            ]);
        }

        return redirect()->route('cart.index')->with('sukses', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, Cart $cart)
    {
        $this->authorizeCartOwner($cart);

        $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $barang = $cart->barang;

        if ($barang->stok < $request->qty) {
            return back()->withErrors(['qty' => 'Stok tidak mencukupi.']);
        }

        $cart->update(['qty' => $request->qty]);

        return redirect()->route('cart.index')->with('sukses', 'Keranjang berhasil diperbarui!');
    }

    public function destroy(Cart $cart)
    {
        $this->authorizeCartOwner($cart);

        $cart->delete();

        return redirect()->route('cart.index')->with('sukses', 'Produk berhasil dihapus dari keranjang!');
    }

    private function authorizeCartOwner(Cart $cart): void
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke keranjang ini.');
        }
    }
}
