<?php

namespace App\Http\Controllers\Front;

use App\AllStatic;
use App\Http\Controllers\Controller;
use App\Http\Resources\Offer\CampaignResource;
use App\Http\Resources\Product\BrandResource;
use App\Http\Resources\Product\CategoryResource;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\SubCategoryResource;
use App\Http\Resources\Product\SubSubCategoryResource;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting\PageSetting;
use App\Models\SubCategory;
use App\Models\SubCategoryBrand;
use App\Models\Subscribe;
use App\Models\SubSubCategory;
use App\Repository\ProductRepository;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class WebController extends Controller
{
    protected $productRepository;
    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepository = $productRepo;
    }

    public function index()
    {
        return view('front.index');

    }

    public function ShowPage($slug)
    {
        $data = PageSetting::where('title', str_replace('-', ' ', $slug))->first();
        return view('front.include.page', ['data' => $data]);
    }

    public function categoryList()
    {

        $category = Category::where('status', '=', AllStatic::$active)->get();

        return CategoryResource::collection($category);

    }

    

    public function productByCategoryList()
    {
        // $category = Category::with(['some_product' => function ($query) {
        //     return $query->latest()->take(6)->get();
        //    }])
        //    ->whereHas('some_product')
        //    ->paginate(1);


        $category = Category::with('some_product')
        ->whereHas('some_product')
        ->paginate(1);
                //    ->get()
                //    ->toSql();

        return CategoryResource::collection($category);
    }

    // public function productByCategory($id, $slug)
    // {
    //     $category = new CategoryResource(Category::with(['sub_category' => function ($query) {
    //         $query->where('status', '=', AllStatic::$active);
    //     }])
    //             ->find($id));

    //     return view('front.cateogry.all_product_category', ['category' => $category]);
    // }

    public function homeOffers()
    {

        $campaign = Campaign::select('id', 'title', 'image')
            ->orderBy('updated_at', 'desc')
            ->where('status', '=', 1)
            ->take(4)
            ->get();

        return CampaignResource::collection($campaign);

    }

    public function productList(Request $request)
    {
        // all product will response from repo with all filtering from different palce
        return $this->productRepository->getFrontProduct($request);
    }

    public function searchPage(Request $request)
    {
        // return $this->productList($request);
        return view('front.search_page');
    }

    // front category sub category maintaning

    public function subscribe(Request $request)
    {
        // return $request->all();

        $request->validate([
            'email' => 'required|email|unique:subscribers',
        ]);
        try {
            Subscribe::create($request->all());
            // \Session::flash('message', 'You Subscribed Successfully, Thank You!');
            Session::flash('alert-class', 'alert-danger'); 
            return back()->with('message', 'You Successfully Subscribed , Thank You!');
        } catch (\Exception $e) {
            // return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            return back()->with('message','Something went wrong, Try again!');
        }

    }

    public function categoryProduct($id, $slug)
    {
        $category = new CategoryResource(Category::with(['sub_category' => function ($query) {
            $query->where('status', '=', AllStatic::$active);
        }])
                ->find($id));

        return view('front.cateogry.category', ['category' => $category]);

    }

    public function subCategory($id, $slug = null)
    {

        $sub_category = SubCategory::with(['category:id,category_name',
            'sub_sub_category' => function ($query) {
                $query->where('status', '=', AllStatic::$active);
            }])
            ->find($id);

        return view('front.subcategory.sub_category',
            [
                'sub_category' => new SubCategoryResource($sub_category),
            ]);

    }

    // sub sub category or level three

    public function subSubCategory($id, $slug = null)
    {

        $sub_sub_category = SubSubCategory::with('sub_category')
            ->find($id);

        $brand_id = SubCategoryBrand::where('sub_sub_category_id', '=', $id)
            ->where('status', '=', AllStatic::$active)
            ->pluck('brand_id');

        $sub_sub_category_brand = Brand::select('id', 'brand_name', 'brand_native_name', 'brand_logo')
            ->orderBy('brand_name', 'asc')
            ->whereIn('id', $brand_id)->where('status', AllStatic::$active)->get();

        return view('front.sub-sub-category.sub_sub_category',
            [
                'sub_sub_category' => new SubSubCategoryResource($sub_sub_category),
                'brands'           => BrandResource::collection($sub_sub_category_brand),
            ]);

    }

    public function productDetails($id, $slug)
    {
        $product = Product::with([
            'category:id,category_name',
            'sub_category:id,sub_category_name',
            'brand:id,brand_name',
            'size',
            'color',
            'multiple_image',
        ])->find($id);

        return view('front.product.single_product', ['product' => new ProductResource($product)]);
    }

}
