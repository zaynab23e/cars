<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Image;
use App\Http\Resources\ModelResource;

class CarController extends Controller
{
    //_______________________________________________________________________________________________
    public function index($brandId, $typeId, $modelId)
    {
        $cars = Car::where('carmodel_id', $modelId)->get();
        return response()->json($cars);
    }

    //_____________________________________________________________________________________________
public function store(Request $request, $brandId, $typeId, $modelId)
{
    $model = CarModel::where('id', $modelId)
        ->where('type_id', $typeId)
        ->firstOrFail();

    if (!$model) {
        return response()->json(['message' => 'الموديل غير موجود أو غير مرتبط بنوع/براند صالح'], 404);
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
        $imagePath = $request->file('image')->store('cars', 'public');
    }


    $car = Car::create([
        'carmodel_id' => $modelId,
        'plate_number' => $request->plate_number,
        'status' => $request->status,
        'color' => $request->color,
        'image' => $imagePath,
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
        'message' => 'تم إضافة السيارة والصور بنجاح',
        'data' => $car->load('images'), 
    ], 201);
}

    // _________________________________________________________________________________________

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
//________________________________________________________________________________________________________
public function related()
{
    $cars = CarModel::inRandomOrder()->take(7)->get();
    return response()->json($cars);
}
// __________________________________________________________________________________________
public function update(Request $request, $brandId, $typeId, $modelId, Car $car)
{
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
        $car->image = $request->file('image')->store('cars', 'public');
    }


    $car->update([
        'plate_number' => $request->plate_number ?? $car->plate_number,
        'status' => $request->status ?? $car->status,
        'color' => $request->color ?? $car->color,
        'image' => $car->image, 
    ]);

    return response()->json([
        'message' => 'تم تحديث السيارة بنجاح',
        'data' => [
            'id' => $car->id,
            'plate_number' => $car->plate_number,
            'status' => $car->status,
            'color' => $car->color,
            'main_image' => $car->image ? asset('storage/' . $car->image) : null,
        ]
    ]);
}

    //____________________________________________________________________________________________
    public function destroy($brandId, $typeId, $modelId, Car $car)
    {
        if ($car->carmodel_id != $modelId) {
            return response()->json(['message' => 'Car does not belong to this model'], 404);
        }

        $car->delete();
        $model = CarModel::where('id', $modelId)
                ->where('type_id', $typeId)
                ->firstOrFail();        
        $carsCount = Car::where('carmodel_id',$modelId)->count();
        $model->count = $carsCount ;
        $model->save();         
        return response()->json(['message' => 'Car deleted successfully']);
    }
}
