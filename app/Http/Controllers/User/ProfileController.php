<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModelResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class ProfileController extends Controller
{
    public function updateUserProfile(Request $request)
    {
        $filename = null; // Initialize filename to null
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصرح له'], 403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id . ',id',
            'phone' => 'required|string|max:15|unique:users,phone,' . $user->id . ',id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        // if ($request->hasFile('image')) {
        //     $imagePath = $request->file('image')->store('users', 'public');
        //     $validated['image'] = $imagePath;
        // }
            if ($request->hasFile('image')) {
                // حذف الصورة القديمة لو موجودة
                if ($user->image && file_exists(public_path($user->image))) {
                    unlink(public_path($user->image));
                }
        
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
        
                // نحفظ الصورة في نفس المجلد زي store()
                $file->move(public_path('users'), $filename);
        
                // نسجل نفس المسار زي ما في store
                $user->image = 'users/' . $filename;
                // return response()->json(['message' => 'تم تحديث الصورة بنجاح', 'image' =>$user->image], 200);
            }        
            
            $user->name = $validated['name'];
            $user->last_name = $validated['last_name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->save();
        return response()->json(['message' => 'تم تحديث الملف الشخصي بنجاح', 'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'image' => $user->image ? asset($user->image) : null, // Return full URL for image
            'email' => $user->email,
            'phone' => $user->phone,
        ]], 200);
    }
    public function userProfile()
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصرح له'], 403);
        }
        return response()->json([
            'message' => 'تم استرجاع الملف الشخصي بنجاح',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'image' => $user->image ? asset($user->image) : null, // Return full URL for image
                'email' => $user->email,
                'phone' => $user->phone,
                'location' => $user->location,
            ]
        ]);
    }

    public function bookingList()
    {
        $user = Auth::guard('user')->user();

        $bookings = Booking::with(['car.carModel','carModel']) // eager load car and its model
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $history = $bookings->map(function ($booking) {
            return [
                'booking_id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
                'final_price' => $booking->final_price,
                'status' => $booking->car_id ? 'confirmed' : 'pending',
                'model' => new ModelResource($booking->carModel), // fallback model
                'car' => $booking->car_id
                    ?$booking->car
                    : null,
            ];
        });

        return response()->json([
            'message' => 'تم استرجاع سجل الحجوزات بنجاح',
            'data' => $history
        ]);
    }    
}
