<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\user\Store;
use App\Http\Requests\user\Login;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserAuthController extends Controller
{
    public function register(Store $request)
    {
        $validatedData = $request->validated();

        $user = User::create($validatedData);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => __('messages.register_success'),
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Login $request)
    {
        $validatedData = $request->validated();

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['message' => __('messages.invalid_credentials')], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $request->user()->tokens()->delete();
            return response()->json(['message' => __('messages.logout_success')]);
        }

        return response()->json(['message' => __('messages.not_logged_in')], 401);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => __('messages.user_not_found')], 404);
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

        return response()->json(['message' => __('messages.reset_code_sent')]);
    }

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
            return response()->json(['message' => __('messages.invalid_or_expired_code')], 400);
        }

        return response()->json(['message' => __('messages.code_verified')]);
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
            return response()->json(['message' => __('messages.invalid_or_expired_code')], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => __('messages.user_not_found')], 404);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => __('messages.password_reset_success')]);
    }
}
