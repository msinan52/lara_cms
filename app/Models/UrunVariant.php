<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrunVariant extends Model
{
    protected $table = "urun_variants";
    protected $guarded = [];
    public $timestamps = false;

    public function urunVariantSubAttributes()
    {
        return $this->hasMany(UrunVariantSubAttribute::class, 'variant_id');
    }

    public function urunVariantSubAttributesForSync()
    {
        return $this->belongsToMany(UrunVariantSubAttribute::class, 'urun_variant_sub_attributes', 'variant_id', 'sub_attr_id');
    }

    public static function urunHasVariant($product_id, $subAttributeIdList)
    {
        $variant = false;
        if (!is_null($subAttributeIdList)) {
            $subAttributeIdList = array_filter($subAttributeIdList);
            $subAttributeIdList = array_map('intval', $subAttributeIdList);
            foreach (Urun::with('variants')->find($product_id)->variants as $var) {
                if ($var->urunVariantSubAttributes->pluck('sub_attr_id')->toArray() == $subAttributeIdList) {
                    $variant = $var;
                    break;
                }
            }
        }
        return $variant;
    }

}
