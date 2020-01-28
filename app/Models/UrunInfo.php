<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrunInfo extends Model
{
    protected $table = "urunler_info";
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'properties' => 'array',
        'oems' => 'array',
        'supported_cars' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Urun::class, 'product_id');
    }

    public function brand()
    {
        return $this->belongsTo(UrunMarka::class, 'brand_id')->withDefault();
    }

    public function company()
    {
        return $this->belongsTo(UrunFirma::class, 'company_id')->withDefault();
    }

    public function setPropertiesAttribute($value)
    {
        $properties = [];
        foreach ($value as $array_item) {
            if (!is_null($array_item['key'])) {
                $properties[] = $array_item;
            }
        }
        $this->attributes['properties'] = json_encode($properties);
    }

    public function setOemsAttribute($value)
    {
        $oems = [];
        foreach ($value as $array_item) {
            if (!is_null($array_item['key'])) {
                $oems[] = $array_item;
            }
        }
        $this->attributes['oems'] = json_encode($oems);
    }

    public function setSupportedCarsAttribute($value)
    {
        $sups = [];
        foreach ($value as $array_item) {
            if (!is_null($array_item['parent_marka'])) {
                $array_item['parent_marka'] = intval($array_item['parent_marka']);
                $array_item['parent_model'] = intval($array_item['parent_model']);
                $array_item['parent_kasa'] = intval($array_item['parent_kasa']);
                $array_item['parent_model_yili'] = intval($array_item['parent_model_yili']);
                $array_item['parent_motor_hacmi'] = intval($array_item['parent_motor_hacmi']);
                $array_item['beygir_gucu'] = intval($array_item['beygir_gucu']);
                $sups[] = $array_item;
            }
        }
        $this->attributes['supported_cars'] = json_encode($sups);
    }

}
