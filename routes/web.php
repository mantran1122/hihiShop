<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;

/*
|--------------------------------------------------------------------------
| Web Routes - HIHI SHOP
|--------------------------------------------------------------------------
*/

// Real-time AI Chatbot
Route::post('/chat/message', [ChatbotController::class, 'sendMessage'])->name('chat.message');
Route::get('/chat/history', [ChatbotController::class, 'getHistory'])->name('chat.history');

// Trang chủ & Cửa hàng
Route::get('/', [HomeController::class, 'index'])->name('home');

// Danh mục sản phẩm
Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Chi tiết sản phẩm & Tìm kiếm
Route::get('/products', [ProductController::class, 'index'])->name('product.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/search', [ProductController::class, 'search'])->name('search');

// Giỏ hàng
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
});

// Thanh toán & Đặt hàng COD / VietQR
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'place'])->name('place');
    Route::post('/calculate', [CheckoutController::class, 'calculate'])->name('calculate');
    Route::get('/payment/{code}', [CheckoutController::class, 'qrPayment'])->name('payment.qr');
    Route::get('/payment/{code}/status', [CheckoutController::class, 'checkStatus'])->name('payment.status');
    Route::post('/payment/{code}/simulate', [CheckoutController::class, 'simulatePayment'])->name('payment.simulate');
    Route::get('/success/{code}', [CheckoutController::class, 'success'])->name('success');
});

// Webhook thanh toán (Nhận tín hiệu ngân hàng / PayOS / Casso)
Route::post('/payment/webhook', [CheckoutController::class, 'handleWebhook'])->name('payment.webhook');

// Đơn hàng của người dùng (Customer Orders)
Route::middleware('auth')->prefix('my-orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{code}', [OrderController::class, 'show'])->name('show');
});

// Đánh giá sản phẩm & Danh sách yêu thích (yêu cầu đăng nhập)
Route::middleware('auth')->group(function () {
    Route::post('/product/{product}/review', [ReviewController::class, 'store'])->name('review.store');
    Route::delete('/review/{review}', [ReviewController::class, 'destroy'])->name('review.destroy');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// Dashboard người dùng
Route::get('/dashboard', function () {
    return redirect()->route('orders.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Quản lý Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Panel (Quản trị viên)
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('index');
    
    // Quản lý đơn hàng
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{id}', [AdminController::class, 'orderDetail'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.updateStatus');

    // Quản lý sản phẩm
    Route::get('/products', [AdminController::class, 'products'])->name('products.index');
    Route::post('/products/{id}/toggle-status', [AdminController::class, 'toggleProductStatus'])->name('products.toggleStatus');

    // Quản lý người dùng
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
    Route::post('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('users.updateRole');
});

// Trang lỗi 404
Route::get('/404', function () {
    return response()->view('errors.404', [], 404);
})->name('error.404');

require __DIR__.'/auth.php';
