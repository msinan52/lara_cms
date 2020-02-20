<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referance extends Model
{
    protected $table = 'referanslar';
    protected $guarded = [];
    public $timestamps = false;

    const  IMAGE_QUALITY = 60;
    const  IMAGE_RESIZE = null;
}
