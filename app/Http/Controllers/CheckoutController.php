<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('barang.kategori')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->withErrors('Keranjang belanja kosong.');
        }

        $total = $cartItems->sum(fn($item) => $item->barang->harga * $item->qty);

        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => ['required', 'string', 'max:255'],
        ]);

        $cartItems = Cart::with('barang')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->withErrors('Keranjang belanja kosong.');
        }

        // Check stock
        foreach ($cartItems as $item) {
            if ($item->barang->stok < $item->qty) {
                return back()->withErrors([
                    'qty' => 'Stok barang "' . $item->barang->nama . '" tidak mencukupi.',
                ]);
            }
        }

        $total = $cartItems->sum(fn($item) => $item->barang->harga * $item->qty);

        DB::transaction(function () use ($request, $cartItems, $total) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => 'INV-' . date('YmdHis') . '-' . Auth::id(),
                'status' => 'pending',
                'total_amount' => $total,
                'shipping_address' => $request->shipping_address,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'barang_id' => $item->barang_id,
                    'qty' => $item->qty,
                    'price' => $item->barang->harga,
                ]);

                // Reduce stock
                $item->barang->decrement('stok', $item->qty);
            }

            // Clear cart
            Cart::where('user_id', Auth::id())->delete();
        });

        return redirect()->route('orders.index')->with('sukses', 'Pesanan berhasil dibuat!');
    }

    public function indexOrders()
    {
        if (Auth::user()->isAdmin() || Auth::user()->isSeller()) {
            $orders = Order::with('user.items.barang')->latest()->get();
        } else {
            $orders = Order::with('items.barang')->where('user_id', Auth::id())->latest()->get();
        }

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if (Auth::user()->isAdmin()) {
            $order->load('user.items.barang');
        } elseif (Auth::user()->isSeller()) {
            $order->load('items.barang');
            $order->items = $order->items->filter(fn($item) => $item->barang->seller_id === Auth::id());
        } else {
            $order->load('items.barang');
            if ($order->user_id !== Auth::id()) {
                abort(403);
            }
        }

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSeller()) {
            abort(403, 'Hanya admin atau seller yang dapat mengupdate status pesanan.');
        }

        $request->validate([
            'status' => ['required', 'in:pending,paid,shipped,completed,cancelled'],
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('sukses', 'Status pesanan berhasil diperbarui!');
    }
}
