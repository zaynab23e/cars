<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\bokingStore;
use App\Http\Resources\ModelResource;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\Request;

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


    public function store(bokingStore $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $booking = Booking::create($validated);

        return response()->json(['message' => 'تم إنشاء الحجز بنجاح', 'data' => $booking], 201);
    }
}
