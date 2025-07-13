<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Image;
use App\Http\Resources\ModelResource;
use App\Models\ModelName;
use App\Models\Type;

class CarController extends Controller
{
<<<<<<< HEAD
    public function index($brandId, $typeId, $modelId)
=======
    //_______________________________________________________________________________________________
    public function index(string $brandId, string $typeId, string $modelNameId, string $modelId)
>>>>>>> 83cfaaf94df611c81ab998142fd42a2cfe20f59a
    {
        $cars = Car::where('carmodel_id', $modelId)
        ->whereHas('carModel.modelName', function ($query) use ($modelNameId) {
            $query->where('id', $modelNameId);
        })
        ->get();
        return response()->json($cars);
    }

<<<<<<< HEAD
    public function store(Request $request, $brandId, $typeId, $modelId)
    {
        $model = CarModel::where('id', $modelId)
            ->where('type_id', $typeId)
            ->firstOrFail();

        if (!$model) {
            return response()->json(['message' => trans('messages.model_not_found')], 404);
=======
    //_____________________________________________________________________________________________
public function store(Request $request,string $brandId, string $typeId,string $modelNameId , string $modelId)
{
    $model = CarModel::with('modelName.type.brand')
        ->where('id', $modelId)
        ->whereHas('modelName', function ($query) use ($modelNameId) {
            $query->where('id', $modelNameId);
        })
        ->first();
    if (!$model) {
        return response()->json(['message' => 'الموديل غير موجود أو غير مرتبط بالنوع'], 404);
    }

    $request->validate([
        'plate_number' => 'required|string|unique:cars',
        'status' => 'nullable|string',
        'color' => 'nullable|string',
        'image' => 'nullable|image|max:2048',
        'images' => 'nullable|array',
        'images.*' => 'image|max:2048', // صور متعددة
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $file = $request->file('image');        
        $imagePath = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('cars'), $imagePath);        
    }


    $car = Car::create([
        'carmodel_id' => $modelId,
        'plate_number' => $request->plate_number,
        'status' => $request->status,
        'color' => $request->color,
        'image' => $imagePath ? 'cars/' . $imagePath : null,
    ]);


    $carsCount = Car::where('carmodel_id', $modelId)->count();
    $model->count = $carsCount;
    $model->save();


    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $path = $file->store('cars', 'public');

            Image::create([
                'car_id' => $car->id,
                'path' => $path,
            ]);
        }
    }

    return response()->json([
        'message' => 'تم إضافة السيارة بنجاح',
        'data' => $car->load('images'),
    ], 201);
}

    // _________________________________________________________________________________________

public function show(string $brandId, string $typeId, string $modelNameId, string $modelId, string $id)
{
    $car = Car::with(['images', 'carModel.modelName.type.brand'])->find($id);
    if (!$car) {
        return response()->json(['message' => 'لا توجد سيارة'], 404);
    }

    return response()->json([
        'message' => 'تم استرجاع السيارة بنجاح',
        'data' => [
            'id' => $car->id,
            'plate_number' => $car->plate_number,
            'status' => $car->status,
            'color' => $car->color,
            'main_image' => $car->image ? asset('storage/' . $car->image) : null,
            'images' => $car->images->map(fn($img) => asset('storage/' . $img->path)),
            'car_model' => $car->carModel ? new ModelResource($car->carModel) : null,
        ]
    ]);
}
//________________________________________________________________________________________________________
public function related()
{
    $cars = CarModel::inRandomOrder()->take(7)->get();
    return response()->json($cars);
}
// __________________________________________________________________________________________
public function update(Request $request, string $brandId, string $typeId, string $modelNameId, string $modelId, string $id)
{
    $car = Car::with(['images', 'carModel.modelName.type.brand'])->find($id);
    if (!$car) {
        return response()->json(['message' => 'لا توجد سيارة'], 404);
    }    
    if (!$car) {
        return response()->json(['message' => 'لا توجد سيارة'], 404);
    }    
    if ($car->carmodel_id != $modelId) {
        return response()->json(['message' => 'السيارة لا تنتمي لهذا الموديل'], 404);
    }

    $request->validate([
        'plate_number' => 'sometimes|string|unique:cars,plate_number,' . $car->id,
        'status' => 'sometimes|string',
        'color' => 'nullable|string',
        'image' => 'nullable|image|max:2048',
    ]);

    
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة لو موجودة
        if ($car->image && file_exists(public_path($car->image))) {
            unlink(public_path($car->image));
>>>>>>> 83cfaaf94df611c81ab998142fd42a2cfe20f59a
        }

        $request->validate([
            'plate_number' => 'required|string|unique:cars',
            'status' => 'nullable|string',
            'color' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('cars'), $imagePath);
        }

        $car = Car::create([
            'carmodel_id' => $modelId,
            'plate_number' => $request->plate_number,
            'status' => $request->status,
            'color' => $request->color,
            'image' => $imagePath ? 'cars/' . $imagePath : null,
        ]);

        $model->count = Car::where('carmodel_id', $modelId)->count();
        $model->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('cars', 'public');
                Image::create([
                    'car_id' => $car->id,
                    'path' => $path,
                ]);
            }
        }

        return response()->json([
            'message' => trans('messages.car_created'),
            'data' => $car->load('images'),
        ], 201);
    }

    public function show($id)
    {
        $car = Car::with(['images', 'carModel.type.brand'])->findOrFail($id);

        return response()->json([
            'message' => [
                'id' => $car->id,
                'plate_number' => $car->plate_number,
                'status' => $car->status,
                'color' => $car->color,
                'main_image' => $car->image ? asset('storage/' . $car->image) : null,
                'images' => $car->images->map(fn($img) => asset('storage/' . $img->path)),
                'car_model' => $car->carModel ? new ModelResource($car->carModel) : null,
                'created_at' => $car->created_at,
            ]
        ]);
    }

    public function related()
    {
        $cars = CarModel::inRandomOrder()->take(7)->get();
        return response()->json($cars);
    }

    public function update(Request $request, $brandId, $typeId, $modelId, Car $car)
    {
        if ($car->carmodel_id != $modelId) {
            return response()->json(['message' => trans('messages.car_wrong_model')], 404);
        }

        $request->validate([
            'plate_number' => 'sometimes|string|unique:cars,plate_number,' . $car->id,
            'status' => 'sometimes|string',
            'color' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($car->image && file_exists(public_path($car->image))) {
                unlink(public_path($car->image));
            }

            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('cars'), $filename);
            $car->image = 'cars/' . $filename;
        }

        $car->plate_number = $request->plate_number ?? $car->plate_number;
        $car->status = $request->status ?? $car->status;
        $car->color = $request->color ?? $car->color;

        if (!$car->save()) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => trans('messages.car_update_failed'),
                'data' => null
            ], 500);
        }

        return response()->json([
            'message' => trans('messages.car_updated'),
            'data' => [
                'id' => $car->id,
                'plate_number' => $car->plate_number,
                'status' => $car->status,
                'color' => $car->color,
                'main_image' => $car->image ? asset($car->image) : null,
            ]
        ]);
    }

<<<<<<< HEAD
    public function destroy($brandId, $typeId, $modelId, Car $car)
=======
    //____________________________________________________________________________________________
    public function destroy(string $brandId, string $typeId, string $modelNameId, string $modelId, string $id)
>>>>>>> 83cfaaf94df611c81ab998142fd42a2cfe20f59a
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(['message' => 'لا توجد سيارة'], 404);
        }         
        if ($car->carmodel_id != $modelId) {
<<<<<<< HEAD
            return response()->json(['message' => trans('messages.car_wrong_model')], 404);
=======
            return response()->json(['message' => 'السيارة لا تنتمي لهذا الموديل'], 404);
>>>>>>> 83cfaaf94df611c81ab998142fd42a2cfe20f59a
        }

        $car->delete();
        $model = CarModel::where('id', $modelId)
<<<<<<< HEAD
            ->where('type_id', $typeId)
            ->firstOrFail();

        $model->count = Car::where('carmodel_id', $modelId)->count();
        $model->save();

        return response()->json(['message' => trans('messages.car_deleted')]);
=======
                ->where('model_name_id', $modelNameId)
                ->first();        
        $carsCount = Car::where('carmodel_id',$modelId)->count();
        $model->count = $carsCount ;
        $model->save();         
        return response()->json(['message' => 'تم حذف السيارة بنجاح']);
>>>>>>> 83cfaaf94df611c81ab998142fd42a2cfe20f59a
    }
}
