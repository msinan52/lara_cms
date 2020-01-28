<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $perPage = 20;
    protected $table = "kuponlar";
    public $timestamps = false;
    public $guarded = [];

    public function categories()
    {
        return $this->belongsToMany('App\Models\Kategori', 'kuponlar_kategori', "coupon_id", 'category_id');
    }
}
