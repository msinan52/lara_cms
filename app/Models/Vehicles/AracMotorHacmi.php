<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

class AracMotorHacmi extends Model
{
    protected $perPage = 20;
    protected $table = "arac_motor_hacmi";
    public $timestamps = false;
    public $guarded = [];
}
