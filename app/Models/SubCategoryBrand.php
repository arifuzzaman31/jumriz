<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategoryBrand extends Model
{
    // relation with brand 

    public function brand(){

    	return $this->belongsTo('App\Models\Brand')
    	->withDefault(
         [
         	'id' => 0,
         	'brand_name' => 'unknown',
         ]
    	);
    }

    // relation with subcategory 


    public function sub_sub_category(){

    	return $this->belongsTo('App\Models\SubSubCategory')->withDefault([
          'id' => 0,
          'sub_sub_category_name' => 'unknown',
          'sub_sub_category_native_name' => 'unknown',
    	]);
    }

}
