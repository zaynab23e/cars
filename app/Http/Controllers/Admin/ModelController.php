<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use App\Models\CarModel;
use App\Models\Type;

class ModelController extends Controller
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

        $models = $type->carModels()->with('type.brand')->get(['id', 'name', 'year', 'price', 'image', 'type_id']);
        return response()->json($models);
    }

// ____________________________________________________________
    public function store(string $brandId, string $typeId, Request $request)
    {
        // return response()->json([$request->all()]);
        $request->validate([
            'name' => 'required|string',
            'year' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048'

        ]);
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        } 
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }

        $file = $request->file('image'); // You have an UploadedFile instance
        $filename = time() . '.' . $file->getClientOriginalExtension();

        $filename = $file->store('models', 'public');
        $model = CarModel::create([
            'name' => $request->name,
            'year' => $request->year,
            'price' => $request->price,
            'image' => $filename,
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
        $request->validate([
            'name' => 'required|string',
            'year' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048'
        ]);
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }
        $model = $type->carmodels()->find($id);
        if (!$model) {
            return response()->json(['message' => 'الموديل غير موجود في هذا البراند'], 404);
        }        

        $file = $request->file('image'); // You have an UploadedFile instance
        $filename = time() . '.' . $file->getClientOriginalExtension();

        $filename = $file->store('models', 'public');
        $model->update([
            'name' => $request->name,
            'year' => $request->year,
            'price' => $request->price,
            'image' => $filename,
        ]);

        return response()->json([
            'message' => 'تم إضافة الموديل بنجاح',
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

        $model = CarModel::find($id);
        if (!$model) {
            return response()->json(['message' => 'الموديل غير موجود'], 404);
        }

        if (!$model->type->brand) {
            return response()->json(['message' => 'الموديل لا ينتمي لهذا النوع أو البراند'], 403);
        }

        return response()->json($model);
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
            $model = CarModel::find($id);
            if (!$model) {
                return response()->json(['message' => 'الموديل غير موجود'], 404);
            }

        if (!$model->type->brand) {
            return response()->json(['message' => 'هذا الموديل لا ينتمي لهذا البراند'], 403);
        }

        $model->delete();

        return response()->json([
            'message' => 'تم حذف الموديل بنجاح'
        ]);
    }
//_________________________________________________________________________________________________
}
