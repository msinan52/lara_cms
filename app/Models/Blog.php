<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $perPage = 20;
    protected $table = "blog";
    public $timestamps = true;
    public $guarded = [];
}
