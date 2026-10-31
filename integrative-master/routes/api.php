<?php

use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingRoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;

//Isaac Lim Tian Le
Route::get('/roomTypes', [RoomTypeController::class, 'apiIndex']);

Route::get('/roomTypesToBook', [RoomTypeController::class, 'apiRoomTypesToBook']);

Route::get('/room/availableRoom', [RoomController::class, 'apiGetAvailableRoomByRoomType']);

Route::get('/room/{roomId}/roomType', [RoomController::class, 'apiGetRoomType']);

Route::get('/room/{roomId}', [RoomController::class, 'apiGetRoom']);

Route::get('/promo/{code}', [PromoCodeController::class, 'apiGetPromo']);

// Chin Jia Jun
Route::get('/fullyBookedDates', [BookingRoomController::class, 'fullyBookedDates']);

Route::get('/cartItems', [CartController::class, 'apiGetCartItems']);

Route::get('/bookings', [BookingController::class, 'apiGetBookings']);

Route::get('/bookingRooms', [BookingRoomController::class, 'apiGetBookingRooms']);

//Leow Shi Hao
Route::get('/payments', [PaymentController::class, 'apiGetPayment']);

//Keshandra A/L Jayaselan
Route::get('/admins', [AdminController::class, 'getAdmins']);
Route::get('/admin/{userId}', [AdminController::class, 'getAdminByUserId']);
Route::get('/customer', [CustomerController::class, 'getCurrentCustomer']);

// Route::get('/customer', function () {
//     $user = [ 'id' => 1, 'name' => 'Jane Doe', 'email' => 'chinjiajun191004@gmail.com', 'role' => 'customer', 'created_at' => '2025-09-12T10:00:00Z', ]; 
//     return response()->json($user);
// });

