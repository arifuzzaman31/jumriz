<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\ShopSetting;
use Illuminate\Support\Facades\Storage;

class ShopSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.setting.shop.shop');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'shop_name'   => 'required',
                'address'     => 'required',
                'footer_text' => 'required',
                'phone'       => 'required',
                'email'       => 'required',
                'theme_color' => 'required',
                'header_logo' => 'nullable|image|mimes:jpeg,png,gif,jpg,webp,bmp',
                'favicon'     => 'nullable|image|mimes:jpeg,png,gif,jpg,webp,bmp',
                'footer_logo' => 'nullable|image|mimes:jpeg,png,gif,jpg,webp,bmp',
            ]
        );

        try
        {
            $shop =  ShopSetting::first();
            $shop->shop_name           =  $request->shop_name;
            $shop->shop_short_name     =  $request->shop_short_name;
            $shop->address             =  $request->address;
            $shop->footer_text         =  $request->footer_text;
            $shop->phone               =  $request->phone;
            $shop->email               =  $request->email;
            $shop->return_policy       =  $request->return_policy;
            $shop->facebook            =  $request->facebook_link;
            $shop->twitter             =  $request->twitter_link;
            $shop->youtube             =  $request->youtube_link;
            $shop->theme_color         =  $request->theme_color;
            $shop->hot_deal_status     =  $request->hot_deal_status;
            $shop->slider_status       =  $request->slider_status;
            $shop->onsale_status       =  $request->onsale_status;
            $shop->sidemenu_status     =  $request->sidemenu_status;

            if ($request->hasFile('header_logo')) {
                if (file_exists('images/logo/'.$shop->header_logo) && !empty($shop->header_logo))
                {
                    unlink('images/logo/'.$shop->header_logo);
                }
                $file = $request->file('header_logo');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

                // Move original file directly to the folder (no resizing)
                $file->move(public_path('images/logo'), $fileName);

                $shop->logo_header = $fileName;
            }
            
            if ($request->hasFile('footer_logo')) {
                if (file_exists('images/logo/'.$shop->logo_footer) && !empty($shop->logo_footer))
                {
                    unlink('images/logo/'.$shop->logo_footer);
                }
                $file = $request->file('footer_logo');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('images/logo'), $fileName);

                $shop->logo_footer = $fileName;
            }
            
            if ($request->hasFile('favicon')) {
                if (file_exists('images/logo/'.$shop->favicon) && !empty($shop->favicon))
                {
                    unlink('images/logo/'.$shop->favicon);
                }
                $file = $request->file('favicon');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('images/logo'), $fileName);

                $shop->favicon = $fileName;
            }

            $shop->update();
            // clear cache 
            cache()->forget('shop-info');

            return response()->json(['status' => 'success','message' => 'Shop Setting Updated']);


        }
        catch(\Exception $e)
        {
            return $e;
            return response()->json(['status'=>'error','message'=>'Something Went Wrong !']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shop_setting = ShopSetting::find($id);

        return $shop_setting;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAddress()
    {
        return ShopSetting::find(1);
    }
}
