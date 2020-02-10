<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'icerik_yonetim';
    protected $perPage = 20;
    protected $guarded = [];
}
