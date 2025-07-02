<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\bokingStore;
use App\Models\Booking;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBookingController extends Controller
{
    public function carBooking(string $id, bokingStore $request)
    {
        // return response()->json($request->all());
        $model = CarModel::find($id);
        if (!$model) {
            return response()->json(['message' => 'الموديل غير موجود'], 404);
        }
        $price = $model->price;
        $days = ((strtotime($request->end_date) - strtotime($request->start_date)) / (60 * 60 * 24)) + 1;
        $finalPrice = $price * $days;
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصرح له'], 403);
        }
        $validated = $request->validated();
        $booking = Booking::create([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'final_price' => $finalPrice,
            'user_id' => $user->id,
            'carmodel_id' => $model->id,
        ]);
        return response()->json(['message' => 'تم إنشاء الحجز بنجاح',
         'data' =>[
            'booking' => $booking,
            'user' => $user,
         ] 
        
        ], 201);
    }

    public function userLocation(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        // return response()->json(['request'=>$request->all()]);
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصرح له'], 403);
        }
        $user->location = $validated['location'];
        $user->latitude = $validated['latitude'];
        $user->longitude = $validated['longitude'];
        $user->save();

        return response()->json(['message' => 'تم تحديث الموقع بنجاح', 'data' => $user], 200);
    }


    public function setPaymentMethod(string $modelId,string $id, Request $request)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,visa',
        ]);
        
        $booking->payment_method = $validated['payment_method'];
        $booking->save();

        return response()->json(['message' => 'تم تحديث طريقة الدفع بنجاح', 'data' => $booking], 200);
    }
    public function setPaymobInfo(string $modelId,string $id, Request $request)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }
        
        $validated = $request->validate([
            'payment_status' => 'required|in:Successful,Pending,Declined',
            'transaction_id' => 'required|string|max:255',
        ]);
        
        $booking->payment_status = $validated['payment_status'];
        $booking->transaction_id = $validated['transaction_id'];
        $booking->save();

        return response()->json(['message' => 'تم تحديث بيانات الدفع بنجاح', 'data' => $booking], 200);
    }
}
