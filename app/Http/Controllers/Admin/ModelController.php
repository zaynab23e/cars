<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarModel; 


class ModelController extends Controller
{
//__________________________________________________________________________________________
    public function index()
    {
        $models = CarModel::with('type')->get();
        return response()->json($models);
    }
//__________________________________________________________________________________________________
    //
    public function getByType($id)
    {
        $models = CarModel::where('type_id', $id)->with('type')->get();
        return response()->json($models);
    }
//___________________________________________________________________________________________________

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'year' => 'required|integer',
            'count' => 'required|integer',
            'price' => 'required|numeric',
            'type_id' => 'required|exists:types,id',
        ]);

        $model = CarModel::create($request->all());
        
        return response()->json(['message' => 'تم إضافة الموديل بنجاح', 'data' => $model]);
    }
    
// ____________________________________________________________________________________________________________


    public function update(Request $request, $id)
    {
        $model = CarModel::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string',
            'year' => 'sometimes|integer',
            'count' => 'sometimes|integer',
            'price' => 'sometimes|numeric',
            'type_id' => 'sometimes|exists:types,id',
        ]);

        $model->update($request->all());

        return response()->json(['message' => 'تم تعديل الموديل بنجاح', 'data' => $model]);
    }

    
// ____________________________________________________________________________________________________________
    public function destroy($id)
    {
        $model = CarModel::findOrFail($id);
        $model->delete();
        
        return response()->json(['message' => 'تم حذف الموديل بنجاح']);
    }
// ____________________________________________________________________________________________________________
}
