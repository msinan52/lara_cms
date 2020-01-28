<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class UrunAttribute extends Model
{
    protected $table = "urun_attributes";
    protected $guarded = [];
    public $timestamps = false;

    public static function getActiveAttributesWithSubAttributesCache()
    {
        $cache = Cache::get('cacheActiveAttributesWithSubAttributes');
        if (is_null($cache))
            $cache = self::setCache(UrunAttribute::with('subAttributes')->where(['active' => 1])->get());
        return $cache;
    }

    public static function setCache($data)
    {
        return Cache::rememberForever('cacheActiveAttributesWithSubAttributes', function () use ($data) {
            return $data;
        });
    }

    public static function clearCache()
    {
        Cache::forget('cacheActiveAttributesWithSubAttributes');
        UrunSubAttribute::clearCache();
        return self::getActiveAttributesWithSubAttributesCache();
    }

    public function subAttributes()
    {
        return $this->hasMany(UrunSubAttribute::class, 'parent_attribute');
    }

    public function subAttributeForSync()
    {
        return $this->belongsToMany(UrunSubAttribute::class, 'urun_sub_attributes', 'parent_attribute', 'id');
    }
}
