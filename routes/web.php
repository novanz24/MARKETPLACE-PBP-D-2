<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Halaman utama (Katalog Produk) untuk semua orang
Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/products/{product}', [CatalogController::class, 'show'])->name('products.show');

// Rute Dashboard bawaan Laravel, ini akan berfungsi kembali
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rute yang memerlukan login
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Keranjang (Cart)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/destroy/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'form'])->name('checkout.form');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

// Rute khusus Admin
Route::middleware(['auth', 'can:admin-area'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', Admin\ProductController::class)->except(['show']);
    Route::resource('categories', Admin\CategoryController::class)->except(['show']);
    // Nanti kita tambahkan rute pesanan di sini
});


// Memuat rute autentikasi bawaan
require __DIR__.'/auth.php';