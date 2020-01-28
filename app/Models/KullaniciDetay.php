<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KullaniciDetay extends Model
{
    protected $table = 'kullanici_detay';
    public $timestamps = false;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\Kullanici');
    }

    public function address()
    {
        return $this->belongsTo(KullaniciAdres::class, 'default_address', 'id');
    }

    public function invoiceAddress()
    {
        return $this->belongsTo(KullaniciAdres::class, 'default_invoice_address', 'id');
    }

}
