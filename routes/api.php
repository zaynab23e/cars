<?php

use App\Http\Controllers\Admin\AdminBrandsController;
use App\Http\Controllers\Admin\AdminsAuthController;
use App\Http\Controllers\Admin\AdminTypesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\User\UserAuthController;
use App\Http\Controllers\Admin\ModelController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\User\UserBookingController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\User\HomePageController;
use App\Http\Controllers\User\ProfileController;

Route::middleware('lang')->group(function () {
///////////////////////////////Admin Routes////////////////////////////////////
Route::prefix('/admin')->group(function () {
    Route::post('/register', [AdminsAuthController::class, 'register'])->name('admins.register');
    Route::post('/login', [AdminsAuthController::class, 'login'])->name('admins.login');
});
Route::middleware('admin')->prefix('/admin')->group(function () {
    Route::post('/logout', [AdminsAuthController::class, 'logout'])->name('admins.logout');
    //Brands
    Route::get('/Brands',[AdminBrandsController::class,'index']);   
    Route::get('/Brands/{id}',[AdminBrandsController::class,'show']);   
    Route::post('/Brands',[AdminBrandsController::class,'store']);   
    Route::post('/Brands/{id}',[AdminBrandsController::class,'update']);   
    Route::delete('/Brands/{id}',[AdminBrandsController::class,'destroy']); 
    #Types  
    Route::get('/Brands/{brand}/Types',[AdminTypesController::class,'index']);   
    Route::get('/Brands/{brand}/Types/{id}',[AdminTypesController::class,'show']);   
    Route::post('/Brands/{brand}/Types',[AdminTypesController::class,'store']);   
    Route::post('/Brands/{brand}/Types/{id}',[AdminTypesController::class,'update']);   
    Route::delete('/Brands/{brand}/Types/{id}',[AdminTypesController::class,'destroy']);  
    
    // Models CRUD (nested in brand & type)
    Route::get('/Brands/{brandId}/Types/{typeId}/Models', [ModelController::class, 'index']);
    Route::post('/Brands/{brandId}/Types/{typeId}/Models', [ModelController::class, 'store']);
    Route::post('/Brands/{brandId}/Types/{typeId}/Models/{id}', [ModelController::class, 'update']);
    Route::delete('/Brands/{brandId}/Types/{typeId}/Models/{id}', [ModelController::class, 'destroy']);
    Route::get('/Brands/{brandId}/Types/{typeId}/Models/{id}', [ModelController::class, 'show']);

    //Booking
    Route::get('/Booking', [BookingController::class, 'index']); 
    Route::post('/Booking', [BookingController::class, 'store']); 
    Route::get('/Booking/{id}', [BookingController::class, 'show']); 
    Route::post('/Booking/{id}', [BookingController::class, 'update']); 
    Route::delete('/Booking/{id}', [BookingController::class, 'destroy']); 


    //car
    Route::get('Brands/{brandId}/Types/{typeId}/Models/{modelId}/Cars', [CarController::class, 'index']);
    Route::post('Brands/{brandId}/Types/{typeId}/Models/{modelId}/Cars', [CarController::class, 'store']);
    Route::get('Brands/{brandId}/Types/{typeId}/Models/{modelId}/Cars/{car}', [CarController::class, 'show']);
    Route::post('Brands/{brandId}/Types/{typeId}/Models/{modelId}/Cars/{car}', [CarController::class, 'update']); 
    Route::delete('Brands/{brandId}/Types/{typeId}/Models/{modelId}/Cars/{car}', [CarController::class, 'destroy']);
    
    //    Route::get('/Brands/{brandId}/Types/{typeId}/Models/{modelId}/Car', [ModelController::class, 'index']);
    
});
// user routes


Route::prefix('/user')->group(function () {

    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('/verify-code', [UserAuthController::class, 'verifyCode']);
    Route::post('/reset-password', [UserAuthController::class, 'resetPassword']);
    Route::get('/related', [CarController::class, 'related']);
    Route::post('/Home', [HomePageController::class, 'index']);
    Route::get('/Model/{id}', [HomePageController::class, 'show']);
    Route::get('/filter-Info', [HomePageController::class, 'filterInfo']);

});


    
});
///////////////////////////////User Routes////////////////////////////////////
Route::middleware('user')->prefix('/user')->group(function () {
    Route::post('/user-location', [UserBookingController::class, 'userLocation']);
    Route::post('/Model/{id}/car-booking', [UserBookingController::class, 'carBooking']);
    Route::post('/Model/{modelId}/car-booking/{id}/payment-method', [UserBookingController::class, 'setPaymentMethod']);
    Route::post('/Model/{modelId}/car-booking/{id}/paymob-info', [UserBookingController::class, 'setPaymobInfo']);
    Route::post('/update-profile', [ProfileController::class, 'updateUserProfile']);    
    Route::get('/booking-list', [ProfileController::class, 'bookingList']);

/////////////////////////Sales/////////////////////////
    Route::post('/Booking/{id}/assign-driver', [BookingController::class, 'assignDriver']);
    Route::post('/Booking/{id}/change-status', [BookingController::class, 'changeStatus']);
    Route::get('/TEST!!!!', [UserBookingController::class, 'TEST']);
//////////////////////////////////////////////////////e




    Route::post('logout', [UserAuthController::class, 'logout']);
});


