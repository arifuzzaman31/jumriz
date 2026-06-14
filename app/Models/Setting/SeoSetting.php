<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    // relation with seo keyword 

    public function seo_keyword(){

    	return $this->hasMany('App\Models\Setting\SeoKeyword');
    }
}
