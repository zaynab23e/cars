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
        $model = CarModel::with('modelName.type.brand')->find($id);
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
        
        if ($request->additional_driver == true) {
            $request->validate([
                'location_id' => 'required|exists:user_locations,id',
            ]);

            if ($request->has('location_id')) {
                $location = $user->userLocations()->find($validated['location_id']);

                if (!$location) {
                    return response()->json(['message' => 'الموقع غير موجود'], 404);
                }

            }
             else {
                return response()->json(['message' => 'يجب تحديد الموقع للمستخدم'], 422);
            }
            // $finalPrice += 100; // Assuming an additional charge for an additional driver
        }
        $booking = Booking::create([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'final_price' => $finalPrice,
            'status' => 'initiated',
            'user_id' => $user->id,
            'carmodel_id' => $model->id,
            'additional_driver' => $request->additional_driver,
        ]);
            if (isset($location)) {
                $booking->location_id = $location->id;
                $booking->save();
                
            }        
            //  return response()->json(['location'=>$booking->location->is_active]);   
            $booking->load(['user','location','carmodel.modelName.type.brand','driver']);

            return response()->json(['message' => 'تم إنشاء الحجز بنجاح',
         'data' =>[
            'booking' => $booking,
         ]

        ], 201);
    }

    public function setPaymentMethod(string $modelId,string $id, Request $request)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }
        if ($booking->status !== 'initiated') {
            return response()->json(['message' => 'لا يمكن تحديث طريقة الدفع إلا في حالة الحجز المبدئي'], 400);
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,visa',
        ]);
        
        
        if ($validated['payment_method'] === 'cash') {
            
            $booking->status = 'confirmed'; // Update status to confirmed
        }else{
            $booking->status = 'awaiting_payment'; // Update status to awaiting payment
        }
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
        if ($booking->status !== 'awaiting_payment') {
            return response()->json(['message' => 'لا يمكن تحديث معلومات الدفع إلا في حالة انتظار الدفع'], 400);
        }
        
        $validated = $request->validate([
            'payment_status' => 'required|in:Successful,Pending,Declined',
            'transaction_id' => 'required|string|max:255',
        ]);
        
        switch ($validated['payment_status']) {
            case 'Successful':
                $booking->status = 'confirmed';
                break;

            case 'Pending':
                $booking->status = 'payment_pending';
                break;

            case 'Declined':
                $booking->status = 'canceled';
                break;

            default:
                $booking->status = 'awaiting_payment'; // fallback or initial
                break;
        }

        $booking->payment_status = $validated['payment_status'];
        $booking->transaction_id = $validated['transaction_id'];
        $booking->save();
        return response()->json(['message' => 'تم تحديث بيانات الدفع بنجاح', 'data' => $booking], 200);

    }
    //hrf3ihjr93ujr
}
