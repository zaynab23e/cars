<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandType;
use App\Models\Car;
use Illuminate\Http\Request;
use App\Models\CarModel;
use App\Models\Type;

class ModelController extends Controller
{
// ____________________________________________________________
    public function index(string $brandId, string $typeId)
    {

        $models = CarModel::with('type')->get();
        return response()->json($models);
    }

// ____________________________________________________________
    public function store(string $brandId, string $typeId, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'year' => 'required|integer',
            'price' => 'required|numeric',
        ]);
        $brand = Brand::findOrFail($brandId);
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }


        $model = CarModel::create([
            'name' => $request->name,
            'year' => $request->year,
            'price' => $request->price,
            'type_id' => $type->id,
        ]);

        return response()->json([
            'message' => 'تم إضافة الموديل بنجاح',
            'data' => $model
        ]);
    }
// ____________________________________________________________
public function update(string $brandId, string $typeId, Request $request, $id)
{

    $model = CarModel::findOrFail($id);

    if (!$model) {
        return response()->json(['message' => 'هذا الموديل لا ينتمي لهذا البراند'], 403);
    }


    $request->validate([
        'name' => 'sometimes|string',
        'year' => 'sometimes|integer',
        'count' => 'sometimes|integer',
        'price' => 'sometimes|numeric',
        'type_id' => 'sometimes|exists:types,id',
    ]);

    $updateData = $request->only(['name', 'year', 'count', 'price', 'type_id']);

    if ($request->has('type_id') && $request->type_id != $model->type_id) {
        $newType = Type::where('id', $request->type_id)
                    ->where('brand_id', $brandId)
                    ->first();

        if (!$newType) {
            return response()->json(['message' => 'النوع الجديد لا ينتمي لهذا البراند'], 422);
        }
    }

    $model->update($updateData);

    return response()->json([
        'message' => 'تم تعديل الموديل بنجاح',
        'data' => $model
    ]);
}
    // ____________________________________________________________
    public function show(string $brandId, string $typeId, $id)

{

    $type = Type::where('id', $typeId)->get();

    if (!$type) {
        return response()->json(['message' => 'النوع لا يتبع هذا البراند'], 404);
    }

    $model = CarModel::findOrFail($id);

    if (!$model->type->brands ) {
        return response()->json(['message' => 'الموديل لا ينتمي لهذا النوع أو البراند'], 403);
    }

    return response()->json($model);
}
// ____________________________________________________________
    public function destroy(string $brandId, string $typeId, $id)
    {
            $brand = Brand::findOrFail($brandId);
            $type = $brand->types()->findOrFail($typeId);
            $model = CarModel::findOrFail($id);

        if (!$model->type->brands) {
            return response()->json(['message' => 'هذا الموديل لا ينتمي لهذا البراند'], 403);
        }

        $model->delete();

        return response()->json([
            'message' => 'تم حذف الموديل بنجاح'
        ]);
    }
//_________________________________________________________________________________________________
}
