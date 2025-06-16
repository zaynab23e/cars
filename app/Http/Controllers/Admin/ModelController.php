<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarModel;
use App\Models\Type;

class ModelController extends Controller
{
    // ____________________________________________________________
    public function index(string $brandId, string $typeId)
    {
        $models = CarModel::whereHas('type', function ($query) use ($brandId, $typeId) {
            $query->where('id', $typeId)->where('brand_id', $brandId);
        })->with('type')->get();

        return response()->json($models);
    }

    // ____________________________________________________________
    public function store(string $brandId, string $typeId, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'year' => 'required|integer',
            'count' => 'required|integer',
            'price' => 'required|numeric',
            'type_id' => 'required|exists:types,id',
        ]);

        $type = Type::where('id', $request->type_id)
                    ->where('brand_id', $brandId)
                    ->first();

        if (!$type) {
            return response()->json(['message' => 'النوع لا ينتمي لهذا البراند'], 422);
        }

        $model = CarModel::create([
            'name' => $request->name,
            'year' => $request->year,
            'count' => $request->count,
            'price' => $request->price,
            'type_id' => $request->type_id,
            'brand_id' => $type->brand_id,
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

        if ($model->type->brand_id != $brandId) {
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

        if ($request->has('type_id')) {
            $type = Type::where('id', $request->type_id)
                        ->where('brand_id', $brandId)
                        ->first();

            if (!$type) {
                return response()->json(['message' => 'النوع الجديد لا ينتمي لهذا البراند'], 422);
            }

            $updateData['brand_id'] = $type->brand_id;
        }
//______________________________________________________________________________
        $model->update($updateData);

        return response()->json([
            'message' => 'تم تعديل الموديل بنجاح',
            'data' => $model
        ]);
    }

    // ____________________________________________________________
    public function destroy(string $brandId, string $typeId, $id)
    {
        $model = CarModel::findOrFail($id);

        if ($model->type->brand_id != $brandId) {
            return response()->json(['message' => 'هذا الموديل لا ينتمي لهذا البراند'], 403);
        }

        $model->delete();

        return response()->json([
            'message' => 'تم حذف الموديل بنجاح'
        ]);
    }
//_________________________________________________________________________________________________
}
