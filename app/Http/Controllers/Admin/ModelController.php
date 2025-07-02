<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use App\Models\CarModel;
use App\Models\Type;
use App\Http\Resources\ModelResource;


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
        return ModelResource::collection($models);
    }

// ____________________________________________________________
    public function store(string $brandId, string $typeId, Request $request)
    {
        $filename = null;
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
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
    
            // نحفظ الصورة في مجلد public/item مباشرة
            $file->move(public_path('models'), $filename);
        }        

        $model = CarModel::create([
            'name' => $request->name,
            'year' => $request->year,
            'price' => $request->price,
            'image' => $filename ? 'models/' . $filename : null,
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
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة لو موجودة
            // if ($model->image && file_exists(public_path($model->image))) {
            //     unlink(public_path($model->image));
            // }
    
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
    
            // نحفظ الصورة في نفس المجلد زي store()
            $file->move(public_path('models'), $filename);
    
            // نسجل نفس المسار زي ما في store
            $model->image = 'models/' . $filename;
        } 
        
            $model->name = $request->name;
            $model->year = $request->year;
            $model->price = $request->price;
            if (!$model->save()) {
                return response()->json([
                    'status' => 'Error has occurred...',
                    'message' => 'Model update failed',
                    'data' => null
                ], 500);
            }            

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

        return new ModelResource($model);
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
