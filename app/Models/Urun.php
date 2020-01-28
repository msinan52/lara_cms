<?php

namespace App\Models;

use App\Models\Kategori;
use App\Observers\UrunObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Urun extends Model
{
    use SoftDeletes;

    protected $table = "urunler";
    protected $guarded = [];
//    protected $guarded = ['slug', 'updated_at', 'created_at', 'deleted_at'];
//    public $timestamps = false;
    public $perPage = 12;
    const PER_PAGE = 12;


    public function categories()
    {
        return $this->belongsToMany('App\Models\Kategori', 'kategori_urun', "product_id", 'category_id');
    }

    public function detail()
    {
        return $this->hasMany(UrunDetail::class, 'product');
    }

    public function info()
    {
        return $this->hasOne(UrunInfo::class, 'product_id', 'id')->withDefault();
    }

    public function variants()
    {
        return $this->hasMany(UrunVariant::class, 'product_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Favori::class, 'favoriler', 'product_id');
    }

    public function images()
    {
        return $this->hasMany(UrunImage::class, 'product');
    }

    public function comments()
    {
        return $this->hasMany(UrunYorum::class, 'product_id')->take(100);
    }
    public function commentsForDelete()
    {
        return $this->belongsToMany(UrunYorum::class, 'urun_yorumlar', 'product_id');
    }

    public function activeComments()
    {
        return $this->hasMany(UrunYorum::class, 'product_id')->where(['active' => 1]);
    }


    public function getLastActive10Comments()
    {
        return $this->hasMany(UrunYorum::class, 'product_id')->where(['active' => 1])->take(10);
    }


    public static function getPerPageStatic()
    {
        return self::PER_PAGE;
    }

    public static function getProductOrderType($orderType)
    {
        if ($orderType == 'yeni') {
            return ['updated_at', 'asc'];
        } else if ($orderType == 'artanfiyat') {
            return ['price', 'asc'];
        } else if ($orderType == 'azalanfiyat') {
            return ['price', 'desc'];
        } else {
            return ['id', 'desc'];
        }
    }

    public static function clearAllActiveProductsWithKeyTitlePriceIdCache()
    {
        $products = Urun::where('active', 1)->get(['id', 'title', 'price']);
        \Cache::forget('allActiveProductsWithKeyTitlePriceId');
        \Cache::set('allActiveProductsWithKeyTitlePriceId', $products);
    }
}
