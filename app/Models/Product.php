<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public function category(){

    	return $this->belongsTo('App\Models\Category')->withDefault([
          'id'                   =>  0,
          'category_name'        =>  'unknown',
          'category_native_name' =>  'unknown',
          'icon'                 =>  'unknown',
    	]);
    }   

     public function sub_category(){

    	return $this->belongsTo('App\Models\SubCategory')->withDefault([
          'id'                       =>  0,
          'sub_category_name'        =>  'unknown',
          'sub_category_native_name' =>  'unknown',
          'icon'                     =>  'unknown',
    	]);
    }

     public function sub_sub_category(){

    	return $this->belongsTo('App\Models\SubSubCategory')->withDefault([
          'id'                           =>  0,
          'sub_sub_category_name'        =>  'N/A',
          'sub_sub_category_native_name' =>  'N/A',
          'icon'                         =>  'N/A',
    	]);
    }

    public function brand(){
      
       return $this->belongsTo('App\Models\Brand')->withDefault([
          'id'                =>  0,
          'brand_name'        =>  'N/A',
          'brand_native_name' =>  'no brand',
          'brand_logo'        =>  'unknown',
       ]);

    }

    public function productKeyword(){

    	return $this->hasMany('App\Models\ProductKeyword');
    }    

    public function multiple_image(){

    	return $this->hasMany('App\Models\ProductImage','product_id','id');
    }

    public function order_details(){

      return $this->hasMany('App\Models\Order\OrderDetails');
      
    }

//     public function size()
//     {
//           return $this->belongsTo('App\Models\Size');
//     }

//     public function color()
//     {
//           return $this->belongsTo('App\Models\Color');
//     }

    public function productSize(){

      return $this->hasMany('App\Models\ProductSize');

    } 

    public function color()
    {
        return $this->belongsToMany('App\Models\Color','product_color');
    } 
    public function size()
    {
        return $this->belongsToMany('App\Models\Size','product_size');
    } 
}
