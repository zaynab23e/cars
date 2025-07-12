<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
//_____________________________________________________________________________________________________________
    public function markAsAssigned(Request $request, $id)
    {
        $booking = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car', 'driver'])->find($id);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        $booking->status = 'assigned';
        $booking->save();

        return response()->json(['message' => __('messages.driver_assigned'), 'data' => $booking], 200);
    }
//_____________________________________________________________________________________________________________
    public function changeStatus(Request $request, $id)
    {
        $booking = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car', 'driver'])->find($id);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,assigned,canceled,completed',
        ]);

        $booking->status = $request->status;
        $booking->save();

        return response()->json(['message' => __('messages.status_updated'), 'data' => $booking], 200);
    }
//_____________________________________________________________________________________________________________
    // public function myBookings()
    // {
    //     $salesId = Auth::id();
    //     $bookings = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car', 'driver'])
    //         ->where('sales_id', $salesId)
    //         ->latest()
    //         ->get();

    //     return response()->json(['data' => $bookings], 200);
    // }
//_____________________________________________________________________________________________________________

    // public function filterByStatus($status)
    // {
    //     $salesId = Auth::id();
    //     $validStatuses = ['pending', 'confirmed', 'assigned', 'canceled', 'completed'];

    //     if (!in_array($status, $validStatuses)) {
    //         return response()->json(['message' => __('messages.invalid_status')], 400);
    //     }

    //     $bookings = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car', 'driver'])
    //         ->where('sales_id', $salesId)
    //         ->where('status', $status)
    //         ->get();

    //     return response()->json(['data' => $bookings], 200);
    // }
//_____________________________________________________________________________________________________________
public function bookingDetails($id)
    {
        $booking = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car', 'driver'])->find($id);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        return response()->json(['data' => $booking], 200);
    }
//_____________________________________________________________________________________________________________
    public function carsByStatus($status)
    {
        $validStatuses = ['pending', 'confirmed', 'assigned', 'canceled', 'completed'];

        if (!in_array($status, $validStatuses)) {
            return response()->json(['message' => __('messages.invalid_status')], 400);
        }

        $bookings = Booking::with(['car', 'car.model', 'car.brand'])
            ->where('status', $status)
            ->whereNotNull('car_id')
            ->get();

        $cars = $bookings->map(function ($booking) {
            return [
                'booking_id' => $booking->id,
                'car_id' => optional($booking->car)->id,
                'plate_number' => optional($booking->car)->plate_number,
                'model' => optional($booking->car->model)->name,
                'brand' => optional($booking->car->brand)->name,
                'status' => $booking->status,
            ];
        });

        return response()->json([
            'message' => __('messages.cars_by_status'),
            'status' => $status,
            'data' => $cars,
        ]);
    }
//_____________________________________________________________________________________________________________
    public function show($id)
    {
        $booking = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car', 'driver'])->find($id);

        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        return response()->json([
            'message' => __('messages.booking_details'),
            'data' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'user' => [
                    'id' => $booking->user->id,
                    'name' => $booking->user->name,
                ],
                'car' => [
                    'id' => optional($booking->car)->id,
                    'plate_number' => optional($booking->car)->plate_number,
                ],
                'driver' => [
                    'id' => optional($booking->driver)->id,
                    'name' => optional($booking->driver)->name,
                ],
                'location' => [
                    'id' => $booking->location->id,
                    'name' => $booking->location->name,
                ],
                'car_model' => optional($booking->carmodel)->model_name,
            ]
        ]);
    }
//______________________________________________________________________________________________________________

}
