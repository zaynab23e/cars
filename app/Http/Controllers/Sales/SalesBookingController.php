<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request;

class SalesBookingController extends Controller
{
    public function ConfirmedBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location'
        ])
        ->where('status', 'confirmed')
        ->orderBy('created_at', 'desc')
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد حجوزات ',
                'data' => []
            ], 404);
        }

        // اختيار الحقول المطلوبة فقط
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date'   => $booking->end_date,
                'status'   => $booking->status,
                'payment_method'   => $booking->payment_method,
                'final_price'   => $booking->final_price,
                'car_model_id' => optional($booking->carModel)->id,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'brand_name'     => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
                'user_name' => optional($booking->user)->name,
                'user_email' => optional($booking->user)->email,
                'location' => optional($booking->location)->name,
            ];
        });

        return response()->json([
            'message' => 'تم استرجاع سجل الحجوزات بنجاح',
            'data' => $data
        ]);
    }
    public function CompletedBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location'
        ])
        ->where('status', 'completed')
        ->orderBy('created_at', 'desc')
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد حجوزات ',
                'data' => []
            ], 404);
        }

        // اختيار الحقول المطلوبة فقط
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date'   => $booking->end_date,
                'status'   => $booking->status,
                'payment_method'   => $booking->payment_method,
                'final_price'   => $booking->final_price,
                'car_model_id' => optional($booking->carModel)->id,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'brand_name'     => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
                'user_name' => optional($booking->user)->name,
                'user_email' => optional($booking->user)->email,
                'location' => optional($booking->location)->name,
            ];
        });

        return response()->json([
            'message' => 'تم استرجاع سجل الحجوزات بنجاح',
            'data' => $data
        ]);
    }
    public function AssignedBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location'
        ])
        ->where('status', 'assigned')
        ->orderBy('created_at', 'desc')
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد حجوزات ',
                'data' => []
            ], 404);
        }

        // اختيار الحقول المطلوبة فقط
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date'   => $booking->end_date,
                'status'   => $booking->status,
                'payment_method'   => $booking->payment_method,
                'final_price'   => $booking->final_price,
                'car_model_id' => optional($booking->carModel)->id,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'brand_name'     => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
                'user_name' => optional($booking->user)->name,
                'user_email' => optional($booking->user)->email,
                'location' => optional($booking->location)->name,
            ];
        });

        return response()->json([
            'message' => 'تم استرجاع سجل الحجوزات بنجاح',
            'data' => $data
        ]);
    }
    public function CanceledBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location'
        ])
        ->where('status', 'canceled')
        ->orderBy('created_at', 'desc')
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد حجوزات ',
                'data' => []
            ], 404);
        }

        // اختيار الحقول المطلوبة فقط
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date'   => $booking->end_date,
                'status'   => $booking->status,
                'payment_method'   => $booking->payment_method,
                'final_price'   => $booking->final_price,
                'car_model_id' => optional($booking->carModel)->id,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'brand_name'     => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
                'user_name' => optional($booking->user)->name,
                'user_email' => optional($booking->user)->email,
                'location' => optional($booking->location)->name,
            ];
        });

        return response()->json([
            'message' => 'تم استرجاع سجل الحجوزات بنجاح',
            'data' => $data
        ]);
    }
    public function getCars(string $bookingId)
    {
        $booking = Booking::with('carModel.cars')->where('status', 'confirmed')->where('id', $bookingId)->first();

        if (!$booking) {
            return response()->json([
                'message' => 'حجز غير موجود',
                'data' => []
            ], 404);
        }

        $cars = $booking->carModel->cars;
        if ($cars->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد سيارات لهذا الموديل',
                'data' => []
            ], 404);
        }
        $cars->load('carModel.modelName');
        $cars = $cars->map(function ($car) {
            return [
                'id' => $car->id,
                'plate_number' => $car->plate_number,
                'status' => $car->status,
                'color' => $car->color,
                'car_model' => $car->carModel ? [
                    'id' => $car->carModel->id,
                    'year' => $car->carModel->year,
                    'name' => $car->carModel->modelName->name,
                    'brand' =>[
                        'id' => $car->carModel->modelName->type->brand->id,
                        'name' => $car->carModel->modelName->type->brand->name,
                    ]
                ] : null,
            ];
        });

        if ($cars->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد سيارات لهذا الموديل',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'تم استرجاع السيارات بنجاح',
            'data' => $cars
        ]);
    }
    public function assignCar(string $bookingId, string $carId)
    {
        $booking = Booking::find($bookingId);
        $car = Car::find($carId);
        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }
        if (!$car) {
            return response()->json(['message' => 'السيارة غير موجودة'], 404);
        }
        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => 'لا يمكن تعيين السيارة إلا في حالة الحجز المؤكد'], 400);
        }
        
        $booking->car_id = $car->id;
        $booking->save();
        // return response()->json(['message' => 'الحجز غير موجود'], 404);

        return response()->json(['message' => 'تم تعيين السيارة بنجاح', 'data' => $booking], 200);
    }
}
