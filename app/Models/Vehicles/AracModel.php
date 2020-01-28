<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

class AracModel extends Model
{
    protected $perPage = 20;
    protected $table = "arac_modeller";
    public $timestamps = false;
    public $guarded = [];

    public function marka()
    {
        return $this->belongsTo(AracMarka::class, 'parent_marka', 'id');
    }
}
