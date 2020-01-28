<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

class AracOemKod extends Model
{
    protected $perPage = 20;
    protected $table = "arac_oem_kodlari";
    public $timestamps = false;
    public $guarded = [];
}
