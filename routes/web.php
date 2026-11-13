<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () {
    return redirect('/katalog');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin panel (hanya admin)
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard')->middleware(['auth', 'admin']);

// User panel (semua user yang login)
Route::get('/user', [UserController::class, 'index'])->name('user.dashboard')->middleware('auth');

// Katalog produk (publik)
Route::get('/katalog', [BarangController::class, 'index'])->name('katalog');
Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');

// CRUD barang (hanya seller yang memiliki produk)
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create')->middleware('auth');
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store')->middleware('auth');
Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit')->middleware('auth');
Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update')->middleware('auth');
Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy')->middleware('auth');

// Cart routes (hanya buyer)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index')->middleware('auth');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store')->middleware('auth');
Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update')->middleware('auth');
Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy')->middleware('auth');

// Checkout routes (hanya buyer)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index')->middleware('auth');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('auth');

// Order routes
Route::get('/orders', [CheckoutController::class, 'indexOrders'])->name('orders.index')->middleware('auth');
Route::get('/orders/{order}', [CheckoutController::class, 'show'])->name('orders.show')->middleware('auth');
Route::put('/orders/{order}/status', [CheckoutController::class, 'updateStatus'])->name('orders.update-status')->middleware('auth');

// Produk resource (P12)
Route::resource('produk', ProdukController::class);
