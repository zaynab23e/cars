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

        return new ModelResource($model);
    }
    public function filterInfo()
    {
        $brands = Brand::get(['id', 'name', 'logo'])
            ->map(function ($brand) {
                return [
                    'id' => (string) $brand->id,
                    'attributes' => [
                        'name' => $brand->name,
                        'logo' => $brand->logo ? asset($brand->logo) : null,
                    ],
                ];
            });
        $types = Type::pluck('name')
            ->map(fn($type) => strtolower($type))
            ->unique()
            ->values()
            ->map(fn($type) => ['name' => $type]);

        $maxPrice = CarModel::max('price');
        $minPrice = CarModel::min('price');

        return response()->json([
            'brands' => $brands,
            'types' => $types,
            'max_price' => $maxPrice,
            'min_price' => $minPrice,
        ]);
    }

/////////////////////////Sales////////////////////////
}
