<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = "campaigns";

    // relation with product 

    public function product()
    {
       return $this->hasMany('App\Models\Product');
    }
}
