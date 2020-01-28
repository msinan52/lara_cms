<?php

namespace App\Models;

use App\Kullanici;
use Illuminate\Database\Eloquent\Model;

class KullaniciAdres extends Model
{
    protected $table = "kullanici_adres";
    protected $guarded = [];
    protected $perPage = 10;


    const TYPE_DELIVERY = 1;
    const TYPE_INVOICE = 2;

    public function City()
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }

    public function Town()
    {
        return $this->belongsTo(Town::class, 'town', 'id');
    }

    public function User()
    {
        return $this->belongsTo(Kullanici::class, 'user', 'id');
    }


}
