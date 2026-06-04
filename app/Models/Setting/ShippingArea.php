<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class ShippingArea extends Model
{
    protected $fillable = ['city','status'];

    // relation with order 

    public function order(){
        
        return $this->hasMany('App\Models\Order\Order');
 
    }

    public function user(){
        return $this->hasMany('App\User');
    }

    // public function admin()
    // {
    //     return $this->hasMany('App\Models\Admin');
    // }
}
