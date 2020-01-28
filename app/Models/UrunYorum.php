<?php

namespace App\Models;

use App\Kullanici;
use Illuminate\Database\Eloquent\Model;

class UrunYorum extends Model
{
    protected $table = "urun_yorumlar";
    protected $guarded = [];
    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo(Urun::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(Kullanici::class, 'user_id', 'id');
    }
}
