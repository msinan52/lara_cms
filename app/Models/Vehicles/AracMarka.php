<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

class AracMarka extends Model
{
    protected $perPage = 20;
    protected $table = "arac_markalar";
    public $timestamps = false;
    public $guarded = [];

    public function modeller()
    {
        return $this->hasMany(AracModel::class, 'parent_marka', 'id');
    }
}
