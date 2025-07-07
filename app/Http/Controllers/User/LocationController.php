<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function getUserLocation()
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصرح له'], 403);
        }

        $locations = $user->userLocations()->get();
        if (!$locations) {
            return response()->json(['message' => 'لا يوجد موقع للمستخدم'], 404);
        }

        return response()->json(['message' => 'تم استرجاع الموقع بنجاح', 'data' => $locations], 200);
    }
    public function getUserActiveLocation()
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصرح له'], 403);
        }

        $activeLocation = $user->userLocations()->where('is_active', true)->first();
        if (!$activeLocation) {
            return response()->json(['message' => 'لا يوجد موقع نشط للمستخدم'], 404);
        }

        return response()->json(['message' => 'تم استرجاع الموقع النشط بنجاح', 'data' => $activeLocation], 200);
    }

    public function setUserLocation(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_active' => 'required|boolean',
        ]);
        // return response()->json(['request'=>$request->all()]);
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصرح له'], 403);
        }
        if ($validated['is_active'] == true) {
            $user->userLocations()->where('is_active', true)->update(['is_active' => false]);
        }

        $user->userLocations()->create([
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_active' => $validated['is_active'],
        ]); 

        return response()->json(['message' => 'تم اضافة موقع بنجاح'], 200);
    }

    public function updateUserLocation(Request $request, $id)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_active' => 'required|boolean',
        ]);

        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصرح له'], 403);
        }
        
        
        $location = $user->userLocations()->find($id);
    
        if (!$location) {
            return response()->json(['message' => 'الموقع غير موجود'], 404);
        }


        if ($validated['is_active'] == true) {
            $user->userLocations()->where('is_active', true)->update(['is_active' => false]);
        }

        $location->update($validated);

        return response()->json(['message' => 'تم تحديث الموقع بنجاح', 'data' => $location], 200);
    }
}
