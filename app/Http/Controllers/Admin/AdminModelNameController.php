<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModelNameResource;
use App\Models\Brand;
use App\Models\ModelName;
use App\Models\Type;
use Illuminate\Http\Request;

class AdminModelNameController extends Controller
{
// ____________________________________________________________
    public function index(string $brandId, string $typeId)
    {
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        }   
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }

        $modelNames = $type->modelNames()->with('type.brand')->get(['id', 'name', 'type_id']);
        return ModelNameResource::collection($modelNames);
    }

// ____________________________________________________________
    public function store(string $brandId, string $typeId, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        } 
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }
      
        $modelName = ModelName::create([
            'name' => $request->name,
            'type_id' => $type->id,
        ]);

        return response()->json([
            'message' => 'تم إضافة اسم الموديل بنجاح',
            'data' => $modelName
        ]);
    }
// ____________________________________________________________
public function update(string $brandId, string $typeId, Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',      
        ]);
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }
        $modelName = $type->modelNames()->find($id);
        if (!$modelName) {
            return response()->json(['message' => 'اسم الموديل غير موجود في هذا البراند'], 404);
        }        
        
            $modelName->name = $request->name;
            if (!$modelName->save()) {
                return response()->json([
                    'status' => 'Error has occurred...',
                    'message' => 'Model update failed',
                    'data' => null
                ], 500);
            }            

        return response()->json([
            'message' => 'تم تعديل اسم الموديل بنجاح',
            'data' => $modelName
        ]);
    }
    // ____________________________________________________________
    public function show(string $brandId, string $typeId, $id)
    {
        $type = Type::where('id', $typeId)->first();

        if (!$type) {
            return response()->json(['message' => 'النوع لا يتبع هذا البراند'], 404);
        }

        $modelName = ModelName::find($id);
        if (!$modelName) {
            return response()->json(['message' => 'اسم الموديل غير موجود'], 404);
        }

        if (!$modelName->type->brand) {
            return response()->json(['message' => 'اسم الموديل لا ينتمي لهذا النوع أو البراند'], 403);
        }

        return new ModelNameResource($modelName);
    }
// ____________________________________________________________
    public function destroy(string $brandId, string $typeId, $id)
    {
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        }
            if (!$brand->types()->find($typeId)) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }
        // $type = $brand->types()->find($typeId);
        $modelName = ModelName::find($id);
        if (!$modelName) {
            return response()->json(['message' => 'اسم الموديل غير موجود'], 404);
        }

        if (!$modelName->type->brand) {
            return response()->json(['message' => ' اسم الموديل هذا لا ينتمي لهذا البراند'], 403);
        }

        $modelName->delete();

        return response()->json([
            'message' => 'تم حذف اسم الموديل بنجاح'
        ]);
    }
//_________________________________________________________________________________________________
}

