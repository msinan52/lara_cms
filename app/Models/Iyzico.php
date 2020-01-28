<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iyzico extends Model
{
    public $timestamps = false;
    protected $table = "iyzico";
    protected $guarded = [];

    protected function siparis()
    {
        return $this->belongsTo(Siparis::class, 'siparis_id', 'id');
    }


    public static function getMdStatusByParam($param)
    {
        $list = Iyzico::mdStatusList();
        return $list[$param];
    }

    public static function getOptions()
    {
        $options = new \Iyzipay\Options();
        $options->setApiKey('sandbox-KSYPTSuSpx0yHk9Ks9KDqB1HWehtxmOR');
        $options->setSecretKey('sandbox-q1hIJbixRBTvmDyxKYu6x5cWEJLkBtoD');
        $options->setBaseUrl("https://sandbox-api.iyzipay.com");
        return $options;
    }

    public static function mdStatusList()
    {
        return [
            0 => '3-D Secure imzası geçersiz veya doğrulama',
            2 => 'Kart sahibi veya bankası sisteme kayıtlı değil',
            3 => 'Kartın bankası sisteme kayıtlı değil',
            4 => 'Doğrulama denemesi, kart sahibi sisteme daha sonra kayıt olmayı seçmiş',
            5 => 'Doğrulama yapılamıyor',
            6 => '3-D Secure hatası',
            7 => 'Sistem Hatası',
            8 => 'Bilinmeyen kart no',
        ];
    }

}
