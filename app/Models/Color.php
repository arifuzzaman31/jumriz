<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    // public function product()
    // {
    //     return $this->hasMany('App\Models\Product');
    // }

    public function product()
    {
        return $this->belongsToMany('App\Model\Product','products');
    }
}
