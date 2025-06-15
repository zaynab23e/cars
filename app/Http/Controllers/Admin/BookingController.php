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
        $bookings = Booking::with(['user', 'car', 'driver'])->latest()->get();
        return response()->json(['data' => $bookings], 200);
    }
    //________________________________________________________________________________________________________
    public function store(bokingStore $request)
    {
        $validated = $request->validated();

        $booking = Booking::create($validated);

        return response()->json(['message' => 'تم إنشاء الحجز بنجاح', 'data' => $booking], 201);
    }
//__________________________________________________________________________________________________________
    public function show($id)
    {
        $booking = Booking::with(['user', 'car', 'driver'])->find($id);

        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }

        return response()->json(['data' => $booking], 200);
    }
//________________________________________________________________________________________________________
public function update(bokingupdate $request, $id)
{
    $booking = Booking::find($id);
    
    if (!$booking) {
        return response()->json(['message' => 'الحجز غير موجود'], 404);
    }
    
    $validated = $request->validated();
    
    $booking->update($validated);
    
    return response()->json(['message' => 'تم تحديث الحجز بنجاح', 'data' => $booking], 200);
}
//________________________________________________________________________________________________________

public function destroy($id)
{
    $booking = Booking::find($id);
    
    if (!$booking) {
        return response()->json(['message' => 'الحجز غير موجود'], 404);
    }
    
    $booking->delete();
    
    return response()->json(['message' => 'تم حذف الحجز بنجاح'], 200);
}

public function assignDriver(Request $request, $id)
{
    $booking = Booking::find($id);
    
    if (!$booking) {
        return response()->json(['message' => 'الحجز غير موجود'], 404);
    }
    
    $request->validate([
        'driver_id' => 'required|exists:drivers,id',
    ]);
    
    $booking->driver_id = $request->driver_id;
    $booking->status = 'assigned';
    $booking->save();
    
    return response()->json(['message' => 'تم تعيين السائق بنجاح', 'data' => $booking], 200);
}
//________________________________________________________________________________________________________

public function changeStatus(Request $request, $id)
{
    $booking = Booking::find($id);
    
    if (!$booking) {
        return response()->json(['message' => 'الحجز غير موجود'], 404);
    }
    
    $request->validate([
        'status' => 'required|in:pending,confirmed,assigned,canceled,completed',
    ]);
    $booking->status = $request->status;
    $booking->save();
    
    return response()->json(['message' => 'تم تحديث حالة الحجز بنجاح', 'data' => $booking], 200);
}
//________________________________________________________________________________________________________
}
