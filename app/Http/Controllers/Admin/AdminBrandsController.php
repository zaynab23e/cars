<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandsRequest;
use App\Http\Resources\BrandsResource;
use App\Models\Brand;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminBrandsController extends Controller
{
    use HttpResponses;
    

    public function index()
    {
        $brands = Brand::with('types')->get(); // Assuming you have a MenuCategory model

        return BrandsResource::collection($brands);
    }

    public function store(Request $request)
    {
        // Store the Brand in the database
        $request->validate([
            'name' => 'required|string|max:255',
            "logo" => 'required'
        ]);
            if ($request->hasFile('logo')) {
            $file = $request->file('logo'); // You have an UploadedFile instance
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('public/brands', $filename);

            $brand = Brand::create([
                'name' => $request->name,
                'logo' => $filename,
            ]);
        }
        if (!$brand) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'Brand creation failed',
                'data' => ''
            ], 500);
        }
        $brand = new BrandsResource($brand);
        return $this->success($brand,'Brand created successfully',201);    }

    public function update(Request $request, $id)
    {
        // return response($request);
        // Logic to update a Brand
        $request->validate([
            'name' => 'required|string|max:255',
            "logo" => 'required'
        ]);        
        // Find the Brand and update it
        $brand = Brand::findOrFail($id);
            if ($request->hasFile('logo')) {
            $file = $request->file('logo'); // You have an UploadedFile instance
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('public/brands', $filename);

            $brand->update([
                'name' => $request->name,
                'logo' => $filename,
            ]);
        }
        if (!$brand) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'Brand creation failed',
                'data' => ''
            ], 500);
        }        
        // $brand->update($request->validated());

        return response()->json([
                'message' => 'Brand Updated Successfully',
                'data' => $brand            
        ]);
    }
    public function destroy($id)
    {
        // Logic to delete a Brand
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return $this->success('','Brand deleted successfully.');
    }
    public function show($id)
    {
        // Logic to display a single Brand
        $brand = Brand::findOrFail($id);
        $admin = Auth::guard('admin')->user();
        return new BrandsResource($brand);
    }

}