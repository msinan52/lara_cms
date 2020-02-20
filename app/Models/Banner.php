<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    protected $perPage = 20;
    protected $table = "banner";
    public $timestamps = true;
    public $guarded = [];

    const  IMAGE_QUALITY = 90;
    const  IMAGE_RESIZE = null;
}
