<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    // relation with category 

    public function category(){

    	return $this->belongsTo('App\Models\Category')->withDefault([
          'id' => 0,
          'category_name' => 'unknown' ,
          'category_native_name' => 'unknown' ,
    	]);
    }

    // relation with sub sub category 
    public function sub_sub_category()
    {
      return $this->hasMany('App\Models\SubSubCategory');
    }


    // relation with sub sub category 



    // relation with subcategory brand 

    // public function sub_category_brand(){

    // 	return $this->hasMany('App\Models\SubCategoryBrand');
    // }

      // relation with product

    public function product(){

      return $this->hasMany('App\Models\Product');
    } 


}
