<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use App\Models\CarModel;
use App\Models\Type;
use App\Http\Resources\ModelResource;
use PhpParser\Node\Expr\Cast\String_;

class ModelController extends Controller
{
// ____________________________________________________________
    public function index(string $brandId, string $typeId,string $modelNameId)
    {
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        }   
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }
        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => 'اسم الموديل غير موجود في هذا النوع'], 404);
        }

        $models = $modelName->carModels()->with('modelName.type.brand')->get(['id', 'year', 'price', 'engine_type', 'transmission_type', 'seat_type', 'seats_count', 'acceleration', 'image', 'model_name_id']);
        return ModelResource::collection($models);
    }

// ____________________________________________________________
    public function store(string $brandId, string $typeId,string $modelNameId, Request $request)
    {
        $filename = null;
        // return response()->json([$request->all()]);
        $request->validate([
            'year' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048',
            'engine_type' => 'required|in:Gasoline,Electric,Hybrid,Plug-in Hybrid',
            'transmission_type' => 'required|in:Manual,Automatic,Hydramatic,CVT,DCT',
            'seat_type' => 'required|in:electric,sport,accessible,leather,fabric',
            'seats_count' => 'required|integer|min:1',
            'acceleration' => 'required|numeric|min:0',
        ]);
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        } 
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }
        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => 'اسم الموديل غير موجود في هذا النوع'], 404);
        }        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
    
            // نحفظ الصورة في مجلد public/item مباشرة
            $file->move(public_path('models'), $filename);
        }        

        $model = CarModel::create([
            'year' => $request->year,
            'price' => $request->price,
            'image' => $filename ? 'models/' . $filename : null,
            'model_name_id' => $modelName->id,
            'engine_type' => $request->engine_type,
            'transmission_type' => $request->transmission_type,
            'seat_type' => $request->seat_type,
            'seats_count' => $request->seats_count,
            'acceleration' => $request->acceleration,

        ]);

        return response()->json([
            'message' => 'تم إضافة الموديل بنجاح',
            'data' => $model
        ]);
    }
// ____________________________________________________________
public function updateImage(string $model, Request $request)
{
    $model = CarModel::find($model);
    $request->validate([
        'image' => 'required|image|max:2048',
    ]);

    if ($request->hasFile('image')) {

        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();

        // نحفظ الصورة في نفس المجلد زي store()
        $file->move(public_path('models'), $filename);

        // نسجل نفس المسار زي ما في store
        $model->image = 'models/' . $filename;
    }
    
    if (!$model->save()) {
        return response()->json([
            'status' => 'Error has occurred...',
            'message' => 'Model update failed',
            'data' => null
        ], 500);
    }

    return response()->json([
        'message' => 'تم تعديل الصورة بنجاح',
        'data' => $model
    ]);
}
public function update(string $brandId, string $typeId,string $modelNameId, Request $request, $id)
    {
        $request->validate([
            'year' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048',
            'engine_type' => 'required|in:Gasoline,Electric,Hybrid,Plug-in Hybrid',
            'transmission_type' => 'required|in:Manual,Automatic,Hydramatic,CVT,DCT',
            'seat_type' => 'required|in:electric,sport,accessible,leather,fabric',
            'seats_count' => 'required|integer|min:1',
            'acceleration' => 'required|numeric|min:0',            
        ]);
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }
        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => 'اسم الموديل غير موجود في هذا النوع'], 404);
        }
        $model = $modelName->carModels()->find($id);
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
        
            $model->year = $request->year;
            $model->price = $request->price;
            $model->engine_type = $request->engine_type;
            $model->transmission_type = $request->transmission_type;
            $model->seat_type = $request->seat_type;
            $model->seats_count = $request->seats_count;
            $model->acceleration = $request->acceleration;
            if (!$model->save()) {
                return response()->json([
                    'status' => 'Error has occurred...',
                    'message' => 'Model update failed',
                    'data' => null
                ], 500);
            }            

        return response()->json([
            'message' => 'تم تعديل الموديل بنجاح',
            'data' => $model
        ]);
    }
    // ____________________________________________________________
    public function show(string $brandId, string $typeId,string $modelNameId, $id)
    {
        $type = Type::where('id', $typeId)->get();

        if (!$type) {
            return response()->json(['message' => 'النوع لا يتبع هذا البراند'], 404);
        }
        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => 'اسم الموديل غير موجود في هذا النوع'], 404);
        }         

        $model = CarModel::find($id);
        if (!$model) {
            return response()->json(['message' => 'الموديل غير موجود'], 404);
        }

        if (!$model->modelName->type->brand) {
            return response()->json(['message' => 'الموديل لا ينتمي لهذا الأسم او النوع أو البراند'], 403);
        }

        return new ModelResource($model);
    }
// ____________________________________________________________
    public function destroy(string $brandId, string $typeId,string $modelNameId, $id)
    {
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => 'البراند غير موجود'], 404);
        }
            if (!$brand->types()->find($typeId)) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => 'النوع غير موجود في هذا البراند'], 404);
        }
        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => 'اسم الموديل غير موجود في هذا النوع'], 404);
        }
        $model = $modelName->carModels()->find($id);
        if (!$model) {
            return response()->json(['message' => 'الموديل غير موجود'], 404);
        }

            $model->delete();

        return response()->json([
            'message' => 'تم حذف الموديل بنجاح'
        ]);
    }
//_________________________________________________________________________________
}
