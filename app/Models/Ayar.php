<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ayar extends Model
{
    protected $table = "ayarlar";
    protected $guarded = [];
    public $timestamps = false;

    public static function setCache($ayarModel)
    {
        return \Cache::rememberForever('siteConfig', function () use ($ayarModel){
            return $ayarModel;
        });
    }

    public static function getCache()
    {
        $cache = \Cache::get('siteConfig');
        if (is_null($cache))
            $cache = self::setCache(Ayar::orderByDesc('id')->first());
        return $cache;
    }
}
