<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\bokingStore;
use App\Http\Resources\BrandsResource;
use App\Http\Resources\ModelResource;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
{
    public function index(Request $request)
    {
        // return response()->json(['request'=>$request->all()]);
        $query = CarModel::with('type.brand')->select('id', 'name', 'year', 'price', 'image', 'type_id');

        if ($request->has('brand') && $request->brand !== null) {
        $query->whereHas('type.brand', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->brand . '%');
        });
        }
        if ($request->has('type') && $request->type !== null) {
            $query->whereHas('type', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->type . '%');
            });
        }
        
        if ($request->has('model') && $request->model !== null) {
            $query->where('name', 'like', '%' . $request->model . '%');
            
        }
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }
        
        

        $models = $query->get();

        return ModelResource::collection($models);
    }
    public function show($id)
    {
        $model = CarModel::with('type.brand')->select('id', 'name', 'year', 'price', 'image', 'type_id')->find($id);
        if (!$model) {
            return response()->json(['message' => 'الموديل غير موجود'], 404);
        }
        if (!$model->type->brand) {
            return response()->json(['message' => 'الموديل لا ينتمي لهذا النوع أو البراند'], 403);
        }

        return response()->json($model);
    }

/////////////////////////Sales////////////////////////
    public function setOrderStatus(string $modelId,string $id, Request $request)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,assigned,canceled,completed',
        ]);
        
        $booking->status = $validated['status'];
        $booking->save();

        return response()->json(['message' => 'تم تحديث طريقة الدفع بنجاح', 'data' => $booking], 200);
    }

}
