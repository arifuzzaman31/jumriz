<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class SeoKeyword extends Model
{
    //

    public function seo_setting(){

    	return $this->belongsTo('App\Models\Setting\SeoSetting');
    }
}
