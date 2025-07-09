<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandsRequest;
use App\Http\Resources\BrandsResource;
use App\Models\Brand;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class AdminBrandsController extends Controller
{
    use HttpResponses;

public function updateImage(string $brandId, Request $request)
{
    $brand = Brand::find($brandId);
    $request->validate([
        'logo' => 'required|image|max:2048',
    ]);

    if ($request->hasFile('logo')) {

        $file = $request->file('logo');
        $filename = time() . '.' . $file->getClientOriginalExtension();

        // نحفظ الصورة في نفس المجلد زي store()
        $file->move(public_path('brands'), $filename);

        // نسجل نفس المسار زي ما في store
        $brand->logo = 'brands/' . $filename;
    }
    
    if (!$brand->save()) {
        return response()->json([
            'status' => 'Error has occurred...',
            'message' => 'brand update failed',
            'data' => null
        ], 500);
    }

    return response()->json([
        'message' => 'تم تعديل الصورة بنجاح',
        'data' => $brand
    ]);
}
    public function index()
    {
        $brands = Brand::with('types')->get(); // Assuming you have a MenuCategory model

        return BrandsResource::collection($brands);
    }

    public function store(Request $request)
    {
        $filename = null;
        // Store the Brand in the database
        $request->validate([
            'name' => 'required|string|max:255',
            "logo" => 'required|image|max:2048'
        ]);
            if ($request->hasFile('logo')) {
            $file = $request->file('logo'); // You have an UploadedFile instance
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('brands'), $filename);


            $brand = Brand::create([
                'name' => $request->name,
                'logo' => $filename ? 'brands/' . $filename : null,
            ]);
        }
        if (!$brand) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'Brand created failed',
                'data' => ''
            ], 500);
        }
        $brand = new BrandsResource($brand);
        return $this->success($brand,'Brand created successfully',201);    }

    public function update(Request $request, $id)
    {
        // return response($request);
        // Logic to update a Brand
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'There is no Brand with this id',
                'data' => ''
            ], 500);
        }        
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            "logo" => 'required|image|max:2048'
        ]);  
            if ($request->hasFile('logo')) {
                // حذف الصورة القديمة لو موجودة
                if ($brand->logo && file_exists(public_path($brand->logo))) {
                    unlink(public_path($brand->logo));
                }
        
                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
        
                // نحفظ الصورة في نفس المجلد زي store()
                $file->move(public_path('brands'), $filename);
        
                // نسجل نفس المسار زي ما في store
                $brand->logo = 'brands/' . $filename;
            }
            
            $brand->name =$request->name;
        // Find the Brand and update it
        //     if ($request->hasFile('logo')) {
        //     $file = $request->file('logo'); // You have an UploadedFile instance
        //     $filename = time() . '.' . $file->getClientOriginalExtension();

        //     $filename = $file->store('brands', 'public');

        //     $brand->update([
        //         'name' => $request->name,
        //         'logo' => $filename,
        //     ]);
        // }
        if (!$brand->save()) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'Brand updated failed',
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
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'There is no Brand with this id',
                'data' => ''
            ], 500);
        }

        $brand->delete();

        return $this->success('','Brand deleted successfully.');
    }
    public function show($id)
    {
        // Logic to display a single Brand
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => 'There is no Brand with this id',
                'data' => ''
            ], 500);
        }  
        return new BrandsResource($brand);
    }

}