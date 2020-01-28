<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Kategori extends Model
{
    use SoftDeletes;

    protected $perPage = 20;
    protected $table = "kategoriler";
    public $timestamps = false;
    public $fillable = ['title', 'active', 'parent_category', 'slug', 'icon', 'spot', 'row'];


    public static function setCache($ayarModel)
    {
        return Cache::rememberForever('cacheCategories', function () use ($ayarModel) {
            return $ayarModel;
        });
    }

    public static function getCache()
    {
        $cache = Cache::get('cacheCategories');
        if (is_null($cache))
            $cache = self::setCache(Kategori::with('sub_categories')->where(['active' => 1, 'parent_category' => null])->orderByRaw('case when kategoriler.row is null then 1 else 0 end,  kategoriler.row')
                ->get());
        return $cache;
    }

    public static function clearCache()
    {
        Cache::forget('cacheCategories');
        Kategori::getCache();
        Kategori::clearAllActiveCategoriesCache();
    }

    public function getProducts()
    {
        return $this->belongsToMany('App\Models\Urun', 'kategori_urun', 'category_id', 'product_id');
    }

    public function parent_cat()
    {
        return $this->belongsTo('App\Models\Kategori', 'parent_category', 'id')->withDefault(['title' => '']);
    }

    public function sub_categories()
    {
        return $this->hasMany(Kategori::class, 'parent_category')->orderBy('row');
    }


    public static function setAllActiveCategoriesCache($ayarModel)
    {
        return Cache::rememberForever('cacheAllActiveCategories', function () use ($ayarModel) {
            return $ayarModel;
        });
    }

    public static function getAllActiveCategoriesCache()
    {
        $cache = Cache::get('cacheAllActiveCategories');
        if (is_null($cache))
            $cache = self::setAllActiveCategoriesCache(Kategori::where(['active' => 1])->get());
        return $cache;
    }

    public static function clearAllActiveCategoriesCache()
    {
        Cache::forget('cacheAllActiveCategories');
        Kategori::getAllActiveCategoriesCache();
    }
}
