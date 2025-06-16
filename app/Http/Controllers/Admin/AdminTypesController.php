<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypesResource;
use App\Models\Brand;
use App\Models\BrandType; 
use App\Models\Type;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTypesController extends Controller
{
    use HttpResponses;
    
    public function index(Brand $brand)
    {
        
        $types = $brand->types()->get(); // Assuming you have a MenuCategory model
        // $types = BrandType::where('brand')->get(); // Assuming you have a MenuCategory model

        return TypesResource::collection($types);
    }

    public function store(Request $request,Brand $brand)
    {
        // Store the Brand in the database
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $type = Type::create([
            'name' => $request->name,
            'description' => $request->description
        ]);
        BrandType::create([
            'brand_id' => $brand->id,
            'type_id' => $type->id,
        ]);
        $type =  new TypesResource($type);
        if (!$type) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'Type Create failed',
                'data' => ''
            ], 500);
        }
        return $this->success($type,'Type created successfully',201);
    }

    public function update(Request $request,Brand $brand,$id)
    {
        // return response($request);
        // Logic to update a Type
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);        
        // Find the Type and update it
        $type = Type::findOrFail($id);
            $type->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
        if (!$type) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'Type Update failed',
                'data' => ''
            ], 500);
        }        
        // $brand->update($request->validated());
        $type = new TypesResource($type);

        return $this->success($type,'Type updated successfully');

    }
        public function destroy(Brand $brand, $typeId)
        {
            // Find the type (optional if you just need to detach by ID)
            $type = Type::findOrFail($typeId);

            // Detach the type from the brand
            $brand->types()->detach($typeId);
            if (!$type) {
                return response()->json([
                    'status' => 'Error has occurred...',
                    'message' => 'There is no Type with this id',
                    'data' => ''
                ], 500);
            }             

            return $this->success('', 'Type detached from brand successfully.');
        }

        public function show(Brand $brand,$id)
        {
            // Logic to display a single Brand
            $type = Type::findOrFail($id);
            if (!$type) {
                return response()->json([
                    'status' => 'Error has occurred...',
                    'message' => 'There is no Type with this id',
                    'data' => ''
                ], 500);
            }             
            return new TypesResource($type);
        }

}
