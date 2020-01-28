<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class UrunSubAttribute extends Model
{
    protected $table = "urun_sub_attributes";
    protected $guarded = [];
    public $timestamps = false;


    public static function getActiveSubAttributesCache()
    {
        $cache = Cache::get('cacheActiveSubAttributesCache');
        if (is_null($cache))
            $cache = self::setCache(UrunSubAttribute::whereHas('attribute', function ($query) {
                $query->where('active', 1);
            })->get());
        return $cache;
    }

    public static function setCache($data)
    {
        return Cache::rememberForever('cacheActiveSubAttributesCache', function () use ($data) {
            return $data;
        });
    }

    public static function clearCache()
    {
        Cache::forget('cacheActiveSubAttributesCache');
        return self::getActiveSubAttributesCache();
    }


    public function attribute()
    {
        return $this->belongsTo(UrunAttribute::class, 'parent_attribute', 'id');
    }
}
