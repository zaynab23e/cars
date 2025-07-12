<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CarModelRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CarModelRatingController extends Controller
{
    public function setRate(Request $request, string $modelId)
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);
        $rating = CarModelRating::updateOrCreate(
            ['user_id' => $user->id, 'car_model_id' => $modelId],
            ['rating' => $request->input('rating'), 'review' => $request->input('review')]
        );
        if (!$rating) {
            return response()->json(['message' => 'فشل في إضافة التقييم'], 500);
        }
        
        return response()->json(['message' => 'تم إضافة التقييم بنجاح'], 201);
    }
    public function resetRate(string $modelId)
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }        
        $rating = CarModelRating::where('user_id', $user->id)
            ->where('car_model_id', $modelId)
            ->firstOrFail();
        if (!$rating) {
            return response()->json(['message' => 'التقييم غير موجود'], 401);
        }

        $rating->delete();

        return response()->json([
            'message' => 'تم حذف التقييم بنجاح',
        ]);        
    }
}
