<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Http\Requests\Admin\bokingStore;
use App\Http\Requests\Admin\bokingupdate;

//________________________________________________________________________________________________________
class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user','location','carmodel.modelName.type.brand','car','driver'])->latest()->get();
        return response()->json(['data' => $bookings], 200);
    }

    public function show($id)
    {
        $booking = Booking::with(['user','location','carmodel.modelName.type.brand','car','driver'])->find($id);

        if (!$booking) {
        return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        return response()->json(['data' => $booking], 200);
    }
//________________________________________________________________________________________________________

public function destroy($id)
{
    $booking = Booking::find($id);
    
    if (!$booking) {
   return response()->json(['message' => __('messages.booking_not_found')], 404);
    }
    
    $booking->delete();
    
     return response()->json(['message' => __('messages.booking_deleted')], 200);
}
//______________________________________________________________________________________________________
public function assignDriver(Request $request, $id)
{
    $booking = Booking::with(['user','location','carmodel.modelName.type.brand','car','driver'])->find($id);

    if (!$booking) {
    return response()->json(['message' => __('messages.booking_not_found')], 404);
    }
    
    // $request->validate([
    //     'driver_id' => 'required|exists:drivers,id',
    // ]);
    
    // $booking->driver_id = $request->driver_id;
    $booking->status = 'assigned';
    $booking->save();
    
      return response()->json(['message' => __('messages.driver_assigned'), 'data' => $booking], 200);
}
//________________________________________________________________________________________________________

public function changeStatus(Request $request, $id)
{
    $booking = Booking::with(['user','location','carmodel.modelName.type.brand','car','driver'])->find($id);

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
}
