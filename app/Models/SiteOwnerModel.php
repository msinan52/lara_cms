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

    const LANG_TR = 1;
    const LANG_EN = 2;
    const LANG_FR = 3;

    public static function languages()
    {
        return [
            self::LANG_TR => [self::LANG_TR, 'Türkçe', true, 'tr', 'tr.png'],
            self::LANG_EN => [self::LANG_EN, 'English', true, 'en', 'en.png'],
            self::LANG_FR => [self::LANG_FR, 'French', false, 'fr', 'fr.png'],
        ];
    }

    public static function getLanguageIdByShortName($shortName)
    {
        $items = collect(self::languages())->filter(function ($item, $key) use ($shortName) {
            if ($item[3] == $shortName)
                return true;
        });
        if (count($items) > 0)
            return $items->first()[0];
        return self::languages()[1][0];
    }

    public static function activeLanguages()
    {
        return collect(self::languages())->filter(function ($item, $key) {
            if ($item[2])
                return true;
        });
    }

    public static function getLabel($langId)
    {
        return isset(self::languages()[$langId][1]) ? self::languages()[$langId][1] : '-';
    }

    public static function getImageNameById($langId)
    {
        return isset(self::languages()[$langId][4]) ? self::languages()[$langId][4] : self::languages()[self::LANG_TR][4];
    }
}
