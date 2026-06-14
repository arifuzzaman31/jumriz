<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class PageSetting extends Model
{
    protected $table = 'pages';
    protected $fillable = ['title','description'];
}
