<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\CategoryResource;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.category.category');
    }

    public function categoryList(Request $request): AnonymousResourceCollection
    {
        $category = Category::orderBy('updated_at', 'desc');

        if ($request->keyword != '') {
            $category->where('category_name', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('category_native_name', 'LIKE', '%' . $request->keyword . '%');

        }

        $category = $category->paginate(10);
        return CategoryResource::collection($category);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:categories,category_name',
            'image' => 'required|image64:jpeg,jpg,png,gif',
        ],
            [
                'image.image64' => 'File must be an image of jpeg,png,gif',
            ]);

        try {
            $category = new Category;

            $category->category_name = $request->name;
            $category->category_native_name = $request->native_name;
            $category->status = $request->status;

            $imageData = $request->get('image');

            if ($imageData) {
                $fileName = storeBase64Image($imageData, 'category/icon');
                $category->icon = $fileName;
            }

            $category->save();

            // clear home page category cache
            Cache::forget('all-category');

            return response()->json(['status' => 'success', 'message' => 'Category Added Successfully !']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Opps Something Went Wrong!', 'debug' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): CategoryResource
    {
        return new CategoryResource(Category::find($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:categories,category_name,' . $id . ',id',
            'image' => 'nullable|image64:jpeg,jpg,png,gif',
        ],
            [
                'image.image64' => 'File must be an image of jpeg,png,gif',
            ]);

        try {
            $category = Category::findOrFail($id);
            $category->category_name = $request->name;
            $category->category_native_name = $request->native_name;
            $category->status = $request->status;

            $imageData = $request->get('image');

            if ($imageData) {
                $oldImagePath = public_path('images/category/icon/' . $category->icon);
                if (!empty($category->icon) && file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }

                $fileName = storeBase64Image($imageData, 'category/icon');
                $category->icon = $fileName;
            }

            $category->save();

            //  clear home page category cache
            Cache::forget('all-category');

            return response()->json(['status' => 'success', 'message' => 'Category  Successfully Updated!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Opps Something Went Wrong!', 'debug' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        // if it has sub category then it can't be deleted
        $count_subcategory = SubCategory::where('category_id', '=', $id)->count();

        if ($count_subcategory > 0) {
            return response()->json(['status' => 'error', 'message' => 'Can\'t delete the category; it has subcategories.']);
        }

        try {
            $category = Category::findOrFail($id);

            $oldImagePath = public_path('images/category/icon/' . $category->icon);
            if (!empty($category->icon) && file_exists($oldImagePath)) {
                @unlink($oldImagePath);
            }

            $category->delete();

            // clear home page category cache
            Cache::forget('all-category');

            return response()->json(['status' => 'success', 'message' => 'Category Deleted']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something Went Wrong !', 'debug' => $e->getMessage()], 500);
        }
    }
}
