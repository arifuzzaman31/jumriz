<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    // relation with subcategory 

    public function sub_category(){

    	return $this->hasMany('App\Models\SubCategory');
    }

    // relation with product

	public function product(){

		return $this->hasMany('App\Models\Product');
    }

	public function some_product(){

		return $this->hasMany('App\Models\Product','category_id','id')
				->limit(6);
				// ->limit(4);
    }
     
	public function size(){

		return $this->hasOne('App\Model\Size');
	} 
}
