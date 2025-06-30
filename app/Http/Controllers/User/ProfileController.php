<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModelResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function updateUserProfile(Request $request)
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصرح له'], 403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:15|unique:users,phone,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
            $validated['image'] = $imagePath;
        }

        $user->update($validated);

        return response()->json(['message' => 'تم تحديث الملف الشخصي بنجاح', 'data' => $user], 200);
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
