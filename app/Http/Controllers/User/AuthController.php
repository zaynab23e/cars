<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\user\store;
use App\Http\Requests\user\login;
use App\Models\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    
    // _________________________________________________________________________________________________________
    public function register(Store $request)
    {
        $validatedData = $request->validated();

        $user = User::create( $validatedData);

        return response()->json(['message'=>'تم التسجيل بنجاح'], 201);
    }

// _______________________________________________________________________________________________________________
    public function login(Login $request)
    {
        $validatedData = $request->validated();

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'=>$user,
            'token' => $token,
        ]);
    }

// _________________________________________________________________________________________________________________
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
        }

        return response()->json(['message' => 'لم يتم تسجيل الدخول'], 401);
    }

// _________________________________________________________________________________________________________________
public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'المستخدم غير موجود'], 404);
    }

    $code = random_int(100000, 999999);

    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        [
            'token' => Hash::make($code),
            'created_at' => now()
        ]
    );

    $user->notify(new \App\Notifications\ResetPassword($code));

    return response()->json(['message' => 'تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني']);
}

// _____________________________________________________________________________________________________________


    public function verifyCode(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'code' => 'required|integer',
    ]);

    $resetEntry = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$resetEntry || !Hash::check($request->code, $resetEntry->token)) {
        return response()->json(['message' => 'الرمز غير صحيح أو منتهي الصلاحية'], 400);
    }

    return response()->json(['message' => 'تم التحقق من الرمز بنجاح']);
}

public function resetPassword(Request $request)
{
    $validatedData = $request->validate([
        'email' => 'required|email',
        'token' => 'required',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $resetEntry = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$resetEntry || !Hash::check($request->token, $resetEntry->token)) {
        return response()->json(['message' => 'الرمز غير صحيح أو منتهي الصلاحية'], 400);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'المستخدم غير موجود'], 404);
    }

    $user->update([
        'password' => Hash::make($request->password)
    ]);

    // حذف الرمز بعد الاستخدام
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return response()->json(['message' => 'تمت إعادة تعيين كلمة المرور بنجاح']);
}

//____________________________________________________________________________________________________________________ 
}


