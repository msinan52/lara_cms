<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteOwnerModel extends Model
{
    protected $table = "site_owner_info";
    protected $guarded = [];
    public $timestamps = false;

    public static function getLast()
    {
        return SiteOwnerModel::orderByDesc('id')->first();
    }
}
