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
Route::post('/chat/message', [ChatbotController::class, 'sendMessage'])->middleware('throttle:40,1')->name('chat.message');
Route::post('/chat/feedback', [ChatbotController::class, 'feedback'])->middleware('throttle:40,1')->name('chat.feedback');
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
    Route::get('/payment/{code}', [CheckoutController::class, 'qrPayment'])->middleware('auth')->name('payment.qr');
    Route::get('/payment/{code}/status', [CheckoutController::class, 'checkStatus'])->middleware('auth')->name('payment.status');
    Route::post('/payment/{code}/simulate', [CheckoutController::class, 'simulatePayment'])->middleware('auth')->name('payment.simulate');
    Route::get('/success/{code}', [CheckoutController::class, 'success'])->middleware('auth')->name('success');
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
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::post('/products/{id}/toggle-status', [AdminController::class, 'toggleProductStatus'])->name('products.toggleStatus');

    // Quản lý voucher
    Route::get('/vouchers', [AdminController::class, 'vouchers'])->name('vouchers.index');
    Route::get('/vouchers/create', [AdminController::class, 'createVoucher'])->name('vouchers.create');
    Route::post('/vouchers', [AdminController::class, 'storeVoucher'])->name('vouchers.store');
    Route::get('/vouchers/{id}/edit', [AdminController::class, 'editVoucher'])->name('vouchers.edit');
    Route::put('/vouchers/{id}', [AdminController::class, 'updateVoucher'])->name('vouchers.update');
    Route::post('/vouchers/{id}/toggle', [AdminController::class, 'toggleVoucher'])->name('vouchers.toggle');

    // Quản lý đánh giá
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews.index');
    Route::post('/reviews/{id}/toggle', [AdminController::class, 'toggleReview'])->name('reviews.toggle');
    Route::delete('/reviews/{id}', [AdminController::class, 'destroyReview'])->name('reviews.destroy');

    // Quản lý danh mục
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::post('/categories/{id}/toggle', [AdminController::class, 'toggleCategory'])->name('categories.toggle');

    // Quản lý thương hiệu
    Route::get('/brands', [AdminController::class, 'brands'])->name('brands.index');
    Route::get('/brands/create', [AdminController::class, 'createBrand'])->name('brands.create');
    Route::post('/brands', [AdminController::class, 'storeBrand'])->name('brands.store');
    Route::get('/brands/{id}/edit', [AdminController::class, 'editBrand'])->name('brands.edit');
    Route::put('/brands/{id}', [AdminController::class, 'updateBrand'])->name('brands.update');
    Route::delete('/brands/{id}', [AdminController::class, 'destroyBrand'])->name('brands.destroy');

    // Quản lý người dùng
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
    Route::post('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('users.updateRole');
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggleStatus');

    // Cài đặt thanh toán (VietQR)
    Route::get('/settings/payment', [AdminController::class, 'paymentSettings'])->name('settings.payment');
    Route::post('/settings/payment', [AdminController::class, 'updatePaymentSettings'])->name('settings.payment.update');
});

// Trang lỗi 404
Route::get('/404', function () {
    return response()->view('errors.404', [], 404);
})->name('error.404');

require __DIR__.'/auth.php';
