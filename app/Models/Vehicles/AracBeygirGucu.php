<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

class AracBeygirGucu extends Model
{
    protected $perPage = 20;
    protected $table = "arac_beygir_gucu";
    public $timestamps = false;
    public $guarded = [];
}
