<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSubCategory extends Model
{

    protected $table = 'sub_sub_categories';
    // protected $primaryKey = 'id';
    // relation with category 

    public function category()
    {
        return $this->belongsTo('App\Models\Category')->withDefault([
            'id' => 0,
            'category_name' => 'unknown' ,
            'category_native_name' => 'unknown' ,
          ]);
    }

    // relation with sub category 

    public function sub_category()
    {
        return $this->belongsTo('App\Models\SubCategory')->withDefault([
            'id' => 0,
            'sub_category_name' => 'unknown' ,
            'sub_category_native_name' => 'unknown' ,
          ]);;
    }
    
    public function sub_sub_category_brand(){

    	return $this->hasMany('App\Models\SubCategoryBrand');
    }

    
    public function product()
    {
        return $this->hasMany('App\Models\Product');
    }

    // relation with order details 

    public function order_details()
    {
        return $this->hasMany('App\Models\Order\OrderDetails');
    }
}
