<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModelResource;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\Request;

class HomePageController extends Controller
{

public function index(Request $request)
{
    $query = CarModel::with('type.brand')->select('id', 'name', 'year', 'price', 'image', 'type_id');
    // Filter by min price
    if ($request->has('min_price') && is_numeric($request->min_price)) {
        $query->where('price', '>=', $request->min_price);
    }

    // Filter by max price
    if ($request->has('max_price') && is_numeric($request->max_price)) {
        $query->where('price', '<=', $request->max_price);
    }

    // Filter by brand name
    if ($request->has('brand') && $request->brand !== null) {
        $query->whereHas('type.brand', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->brand . '%');
        });
    }

    // Filter by type name or type_id
    if ($request->has('type') && $request->type !== null) {
        $query->whereHas('type', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->type . '%');
        });
    }

    $models = $query->get();

    return ModelResource::collection($models);
}


}
