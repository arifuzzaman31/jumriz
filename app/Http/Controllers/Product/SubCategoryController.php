<?php

namespace App\Http\Controllers\Product;

use App\AllStatic;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\SubCategoryResource;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $category = Category::where('status', '=', AllStatic::$active)->get();
        return view('admin.subcategory.subcategory',['category' => $category]);
    }

    public function subCategoryList(Request $request): AnonymousResourceCollection
    {
        $subCategory = SubCategory::with('category:id,category_name')->orderBy('updated_at', 'desc');

        if ($request->keyword != '') {
            $subCategory->where('sub_category_name', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('sub_category_native_name', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($request->category_id != '') {
            $subCategory->where('category_id', $request->category_id);
        }

        $subCategory = $subCategory->paginate(10);
        return SubCategoryResource::collection($subCategory);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|unique:sub_categories,sub_category_name',
            'image' => 'nullable|image64:jpeg,jpg,png,gif',
        ], [
            'image.image64' => 'File must be an image of jpeg,png,gif',
        ]);

        try {
            $subCategory = new SubCategory;
            $subCategory->category_id = $request->category_id;
            $subCategory->sub_category_name = $request->name;
            $subCategory->sub_category_native_name = $request->native_name;
            $subCategory->status = $request->status;

            $imageData = $request->get('image');
            if ($imageData) {
                $fileName = storeBase64Image($imageData, 'category/subcategory/icon');
                $subCategory->icon = $fileName;
            }

            $subCategory->save();

            // Clear relevant caches
            Cache::forget('all-category');

            return response()->json(['status' => 'success', 'message' => 'Sub Category Added Successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Opps Something Went Wrong!', 'debug' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): SubCategoryResource
    {
        return new SubCategoryResource(SubCategory::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|unique:sub_categories,sub_category_name,' . $id . ',id',
            'image' => 'nullable|image64:jpeg,jpg,png,gif',
        ], [
            'image.image64' => 'File must be an image of jpeg,png,gif',
        ]);

        try {
            $subCategory = SubCategory::findOrFail($id);
            $subCategory->category_id = $request->category_id;
            $subCategory->sub_category_name = $request->name;
            $subCategory->sub_category_native_name = $request->native_name;
            $subCategory->status = $request->status;

            $imageData = $request->get('image');
            if ($imageData) {
                $oldImagePath = public_path('images/category/subcategory/icon/' . $subCategory->icon);
                if (!empty($subCategory->icon) && file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }

                $fileName = storeBase64Image($imageData, 'category/subcategory/icon');
                $subCategory->icon = $fileName;
            }

            $subCategory->save();
            Cache::forget('all-category');

            return response()->json(['status' => 'success', 'message' => 'Sub Category Successfully Updated!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Opps Something Went Wrong!', 'debug' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        // Check if it has sub-subcategories
        $count_subsubcategory = SubSubCategory::where('sub_category_id', '=', $id)->count();

        if ($count_subsubcategory > 0) {
            return response()->json(['status' => 'error', 'message' => 'Can\'t delete the sub category; it has sub-subcategories.']);
        }

        try {
            $subCategory = SubCategory::findOrFail($id);

            $oldImagePath = public_path('images/category/subcategory/icon/' . $subCategory->icon);
            if (!empty($subCategory->icon) && file_exists($oldImagePath)) {
                @unlink($oldImagePath);
            }

            $subCategory->delete();
            Cache::forget('all-category');

            return response()->json(['status' => 'success', 'message' => 'Sub Category Deleted']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something Went Wrong!', 'debug' => $e->getMessage()], 500);
        }
    }
}