<?php

use App\Http\Controllers\Admin\AdminBrandsController;
use App\Http\Controllers\Admin\AdminsAuthController;
use App\Http\Controllers\Admin\AdminTypesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ModelController;


Route::middleware('lang')->prefix('/admin')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);


    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return response()->json([
                'message' => __('auth.user_info'),
                'user' => $request->user('admin'), 
            ]);
        });

    });

});




Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminsAuthController::class, 'register'])->name('admins.register');
    Route::post('/login', [AdminsAuthController::class, 'login'])->name('admins.login');

});
Route::middleware('admin')->prefix('admin')->group(function () {
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
        Route::get('/brands/{brandId}/types/{typeId}/models', [ModelController::class, 'index']);
        Route::post('/brands/{brandId}/types/{typeId}/models', [ModelController::class, 'store']);
        Route::put('/brands/{brandId}/types/{typeId}/models/{id}', [ModelController::class, 'update']);
        Route::delete('/brands/{brandId}/types/{typeId}/models/{id}', [ModelController::class, 'destroy']);
});
