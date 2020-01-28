<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

class AracKasa extends Model
{
    protected $perPage = 20;
    protected $table = "arac_kasalar";
    public $timestamps = false;
    public $guarded = [];
}
