<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $table = 'order_details';

    protected $with = ['color','size'];

    public function order()
    {
    	return $this->belongsTo('App\Models\Order\Order');
    }
    
    // relation with product 
    public function product()
    {
    	return $this->belongsTo('App\Models\Product');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand')->withDefault([
            'id' => 0,
            'brand_name' => 'unknown'
        ]);
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category')->withDefault([
            'id' => 0,
            'category_name' => 'unknown'
        ]);
    }

    public function sub_category()
    {
        return $this->belongsTo('App\Models\SubCategory')->withDefault([
            'id' => 0,
            'sub_category_name' => 'unknown'
        ]);
    }

    public function sub_sub_category()
    {
        return $this->belongsTo('App\Models\SubSubCategory')->withDefault([
          'id'                           =>  0,
          'sub_sub_category_name'        =>  'N/A',
          'sub_sub_category_native_name' =>  'N/A',
          // 'icon'                         =>  'N/A'
        ]);
    }

    public function size()
    {
        return $this->belongsTo('App\Models\Size');
    }


    public function color()
    {
        return $this->belongsTo('App\Models\Color');
    }
}

