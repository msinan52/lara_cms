<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "log";
    protected $guarded = [];
    protected $perPage = 20;

    const TYPE_GENERAL = 1;
    const TYPE_SEND_MAIL = 2;
    const TYPE_WRONG_LOGIN = 3;
    const TYPE_CREATE_OBJECT = 4;
    const TYPE_UPDATE_OBJECT = 5;
    const TYPE_DELETE_OBJECT = 6;
    const TYPE_IYZICO = 7;

    public static function listTypesWithId()
    {
        return [
            self::TYPE_GENERAL => [self::TYPE_GENERAL, 'Genel Hata'],
            self::TYPE_SEND_MAIL => [self::TYPE_SEND_MAIL, 'Mail Hatası'],
            self::TYPE_WRONG_LOGIN => [self::TYPE_WRONG_LOGIN, 'Hatalı Giriş'],
            self::TYPE_CREATE_OBJECT => [self::TYPE_CREATE_OBJECT, 'Nesne Oluşturma Hatası'],
            self::TYPE_UPDATE_OBJECT => [self::TYPE_UPDATE_OBJECT, 'Nesne Güncelleme Hatası'],
            self::TYPE_DELETE_OBJECT => [self::TYPE_DELETE_OBJECT, 'Nesne Silme Hatası'],
            self::TYPE_IYZICO => [self::TYPE_IYZICO, 'İyzico Hatası'],
        ];
    }

    public static function typeLabelStatic($param = self::TYPE_GENERAL)
    {
        $list = self::listTypesWithId();
        return @$list[$param][1];
    }


    public static function addLog(string $message, string $exception, $type = Log::TYPE_GENERAL, string $code = null, string $url = null, int $user_id = null)
    {
        Log::create([
            'type' => $type,
            'message' => substr($message, 0, 250),
            'exception' => substr((string)$exception, 0, 65000),
            'user_id' => $user_id == null ? Auth::user() ? Auth::user()->id : 0 : $user_id,
            'code' => $code == null ? str_random(16) : $code,
            'url' => $url == null ? substr(request()->fullUrl(), 0, 150) : substr($url, 0, 150)
        ]);
    }

}
