<?php


use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingRoomController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleLoginController;
use Illuminate\Support\Facades\Route;

// Chin Jia Jun
Route::get('/', function () {
    return view('auth.SignIn');
});
Route::get('/booking/create', [BookingController::class, 'create']);
Route::get('/booking/list', [BookingController::class, 'index']);
Route::get('/roomType/show/{id}', [RoomTypeController::class, 'show']);
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'store']);
Route::delete('/cart/remove', [CartController::class, 'destroy']);
Route::post('/bookingRoom/{id}/cancel', [BookingRoomController::class, 'updateStatus']);

// LEOW SHI HAO 2314003 G3 start
Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
Route::post('/payment/promo', [PaymentController::class, 'applyPromo'])->name('payment.promo');
Route::post('/payment/card', [PaymentController::class, 'processPayment'])->name('payment.card');
Route::post('/payment/tng', [PaymentController::class, 'processPayment'])->name('payment.tng');
Route::post('/payment/update-intent', [PaymentController::class, 'updatePaymentIntent'])->name('payment.update_intent');
Route::get('/payment/receipt', [PaymentController::class, 'showReceipt'])->name('payment.receipt');
// LEOW SHI HAO 2314003 G3 end

//Isaac Lim Tian Le  2314544 G3 start
// Admin routes (Admin and Super Admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

    // RoomType CRUD
    Route::post('/admin/roomType', [AdminDashboardController::class, 'storeRoomType']);
    Route::put('/admin/roomType/{id}', [AdminDashboardController::class, 'updateRoomType']);

    // Room CRUD
    Route::post('/admin/room', [AdminDashboardController::class, 'storeRoom']);
    Route::put('/admin/room/{id}', [AdminDashboardController::class, 'updateRoom']);

    // Promo CRUD
    Route::post('/admin/promocode', [AdminDashboardController::class, 'storePromo']);
    Route::put('/admin/promocode/{id}', [AdminDashboardController::class, 'updatePromo']);
});

// Super Admin routes (only accessible by superadmin)
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::post('/admin/admin', [AdminDashboardController::class, 'storeAdmin']);
    Route::put('/admin/admin/{id}', [AdminDashboardController::class, 'updateAdmin']);
});
//Isaac Lim Tian Le  2314544 G3 end

//Keshandra A/L Jayaselan 2314651 G3 start
Route::get('/signup', function () {
    return view('auth.SignUp');
});

Route::get('/signin', function () {
    return view('auth.SignIn');
});

Route::get('/signup', [RegisterController::class, 'showForm'])->name('signup.form');
Route::post('/signup', [RegisterController::class, 'register'])->name('signup.submit');
Route::get('/welcome', function () {
    return view('welcome'); // Create resources/views/welcome.blade.php
})->name('welcome');

Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

Route::get('/signin', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/signin', [LoginController::class, 'login'])->name('signin.submit');

Route::post('/password/email', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::post('/password/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp.post');
Route::post('/password/update-otp', [ForgotPasswordController::class, 'resetPassword'])->name('password.update.otp');
Route::get('/password/verify-otp', [ForgotPasswordController::class, 'showVerifyOtpForm'])->name('password.verify-otp');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
//Keshandra A/L Jayaselan 2314651 G3 end