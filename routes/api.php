<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

// راوتات API للإدمن
// راوتات API للإدمن باستخدام اللغة
Route::middleware('lang')->prefix('/admin')->group(function () {

    // تسجيل الدخول والتسجيل
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // راوتات محمية بالإثبات وبتأكد إنه أدمن
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return response()->json([
                'message' => __('auth.user_info'),
                'user' => $request->user('admin'), // ضروري نحدد الجارد الخاص بالإدمن
            ]);
        });

    });

});



// Route::group(['prefix' => '{locale}/admin', 'middleware' => ['setAppLang']], function () {

//     Route::post('/register', [AuthController::class, 'register']);
//     Route::post('/login', [AuthController::class, 'login']);

//     Route::middleware(['auth:sanctum', 'admin'])->group(function () {
//         Route::post('/logout', [AuthController::class, 'logout']);

//         Route::get('/user', function (Request $request) {
//             return response()->json([
//                 'message' => __('auth.user_info'),
//                 'user' => $request->user('admin'), // مهم نحدد إنه أدمن
//             ]);
//         });
//     });

// });
