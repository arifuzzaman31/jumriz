<?php

namespace App\Http\Controllers\Product;

use App\AllStatic;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\SubSubCategoryResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SubSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $category = Category::select('id', 'category_name', 'category_native_name')
            ->where('status', '=', AllStatic::$active)
            ->get();

        $brand = Brand::select('id', 'brand_name', 'brand_native_name')
            ->where('status', '=', AllStatic::$active)
            ->orderBy('brand_name', 'asc')
            ->get();

        return view('admin.subsubcategory.sub_sub_category', [
            'category' => $category,
            'brand' => $brand,
        ]);
    }

    public function subSubCategoryList(Request $request): AnonymousResourceCollection
    {
        $sub_category = SubSubCategory::with([
            'category:id,category_name,category_native_name',
            'sub_category:id,sub_category_name,sub_category_native_name',
            'sub_sub_category_brand.brand:id,brand_name'
        ])->orderBy('updated_at', 'desc');

        if (!empty($request->category)) {
            $sub_category->where('category_id', '=', $request->category);
        }
        
        if (!empty($request->sub_category)) {
            $sub_category->where('sub_category_id', '=', $request->sub_category);
        }

        if (!empty($request->keyword)) {
            $sub_category->where(function($query) use ($request) {
                $query->where('sub_sub_category_name', 'LIKE', '%'.$request->keyword.'%')
                      ->orWhere('sub_sub_category_native_name', 'LIKE', '%'.$request->keyword.'%');
            });
        }

        return SubSubCategoryResource::collection($sub_category->paginate(10));
    }

    /**
     * Get sub category by category id
     */
    public function getSubSubCategory(Request $request, $sub_category_id)
    {
        $sub_category = SubSubCategory::select('id', 'sub_sub_category_name', 'sub_sub_category_native_name')
            ->where('sub_category_id', '=', $sub_category_id)
            ->orderBy('sub_sub_category_name', 'asc');

        if (!$request->has('edit_time')) {
            $sub_category->where('status', AllStatic::$active);
        }

        return $sub_category->get();
    }

    /**
     * Get all brand under a sub sub_category
     */
    public function getSubSubCategoryBrand(Request $request, $id)
    {
        $brand_id = DB::table('sub_category_brands')
            ->where('sub_sub_category_id', $id)
            ->where('status', AllStatic::$active)
            ->pluck('brand_id');

        $sub_category_brand = Brand::select('id', 'brand_name', 'brand_native_name')
            ->orderBy('brand_name', 'asc')
            ->whereIn('id', $brand_id);

        if (!$request->has('edit_time')) {
            $sub_category_brand->where('status', AllStatic::$active);
        }

        return $sub_category_brand->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:sub_sub_categories,sub_sub_category_name', // Fixed target table validation bug
            'image' => 'required|image64:jpeg,jpg,png,gif',
            'category' => 'required',
            'sub_category' => 'required',
        ], [
            'image.image64' => 'File must be an image of jpg,jpeg,png,gif',
        ]);

        try {
            DB::beginTransaction();

            $sub_sub_category = new SubSubCategory;
            $sub_sub_category->category_id = $request->category;
            $sub_sub_category->sub_category_id = $request->sub_category;
            $sub_sub_category->sub_sub_category_name = $request->name;
            $sub_sub_category->sub_sub_category_native_name = $request->native_name;
            $sub_sub_category->status = $request->status;

            $imageData = $request->get('image');
            if ($imageData) {
                $fileName = storeBase64Image($imageData, 'sub_sub_category/icon');
                $sub_sub_category->icon = $fileName;
            }

            $sub_sub_category->save();

            if (!empty($request->selected_brand) && count($request->selected_brand) > 0) {
                $insertBrands = [];
                foreach ($request->selected_brand as $value) {
                    $insertBrands[] = [
                        'sub_sub_category_id' => $sub_sub_category->id,
                        'brand_id'            => $value['id'],
                        'status'              => AllStatic::$active, // populated standard schema status tags
                        'created_at'          => now(),
                        'updated_at'          => now()
                    ];
                }
                // SAFE: Write direct through Query Builder
                DB::table('sub_category_brands')->insert($insertBrands);
            }

            DB::commit();
            cache()->forget('all-category');

            return response()->json(['status' => 'success', 'message' => 'Sub Sub Category Added Successfully !']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Opps Something Went Wrong!', 'debug' => $e->getMessage()], 500);
        }
    }

    /**
     * Show data structure for editing.
     */
    public function edit($id): JsonResponse
    {
        $sub_sub_category = SubSubCategory::findOrFail($id);
        $sub_category = SubCategory::where('category_id', $sub_sub_category->category_id)->get();

        $brand_id = DB::table('sub_category_brands')
            ->where('sub_sub_category_id', $id)
            ->where('status', AllStatic::$active)
            ->pluck('brand_id');

        $selected_brand = Brand::select('id', 'brand_name', 'brand_native_name')
            ->orderBy('brand_name', 'asc')
            ->whereIn('id', $brand_id)
            ->get();

        return response()->json([
            'sub_sub_category' => $sub_sub_category,
            'sub_category'     => $sub_category,
            'selected_brand'   => $selected_brand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:sub_sub_categories,sub_sub_category_name,' . $id, // Added unique exemption on self ID
            'image' => 'nullable|image64:jpeg,jpg,png,gif',
            'category' => 'required',
            'sub_category' => 'required',
        ], [
            'image.image64' => 'File must be an image of jpeg,png,gif',
        ]);

        try {
            DB::beginTransaction();

            $sub_subcategory = SubSubCategory::findOrFail($id);
            $sub_subcategory->category_id = $request->category;
            $sub_subcategory->sub_category_id = $request->sub_category;
            $sub_subcategory->sub_sub_category_name = $request->name;
            $sub_subcategory->sub_sub_category_native_name = $request->native_name;
            $sub_subcategory->status = $request->status;

            $imageData = $request->get('image');
            if ($imageData) {
                $oldImagePath = public_path('images/sub_sub_category/icon/' . $sub_subcategory->icon);
                if (!empty($sub_subcategory->icon) && file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
                $fileName = storeBase64Image($imageData, 'sub_sub_category/icon');
                $sub_subcategory->icon = $fileName;
            }

            $sub_subcategory->save();

            // FIX: Using structural DB Table query instead of model delete to eliminate argument mismatch
            DB::table('sub_category_brands')->where('sub_sub_category_id', $id)->delete();

            if (!empty($request->selected_brand) && count($request->selected_brand) > 0) {
                $insertBrands = [];
                foreach ($request->selected_brand as $value) {
                    $insertBrands[] = [
                        'sub_sub_category_id' => $id,
                        'brand_id'            => $value['id'],
                        'status'              => AllStatic::$active,
                        'created_at'          => now(),
                        'updated_at'          => now()
                    ];
                }
                DB::table('sub_category_brands')->insert($insertBrands);
            }

            DB::commit();
            cache()->forget('all-category');

            return response()->json(['status' => 'success', 'message' => 'Sub Sub Category Updated !']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Opps Something Went Wrong!', 'debug' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $count = Product::where('sub_sub_category_id', '=', $id)->count();

            if ($count > 0) {
                return response()->json(['status' => 'error', 'message' => "Can't delete this category, it contains products!"]);
            }

            $sub_category = SubSubCategory::findOrFail($id);

            $oldImagePath = public_path('images/sub_sub_category/icon/' . $sub_category->icon);
            if (!empty($sub_category->icon) && file_exists($oldImagePath)) {
                @unlink($oldImagePath);
            }

            // FIX: Using direct Query Builder delete sequence
            DB::table('sub_category_brands')->where('sub_sub_category_id', $id)->delete();

            $sub_category->delete();
            
            DB::commit();
            cache()->forget('all-category');

            return response()->json(['status' => 'success', 'message' => 'Delete Successful !']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Something Went Wrong !', 'debug' => $e->getMessage()], 500);
        }
    }
}