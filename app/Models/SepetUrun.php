<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SepetUrun extends Model
{
    use  SoftDeletes;
    protected $table = "sepet_urun";
    protected $guarded = [];


    const STATUS_BASARISIZ = 1;
    const STATUS_GERI_ODEME = 2;
    const STATUS_ONAY_BEKLIYOR = 3;
    const STATUS_SIPARIS_ALINDI = 4;
    const STATUS_HAZIRLANIYOR = 5;
    const STATUS_HAZIRLANDI = 6;
    const STATUS_KARGOYA_VERILDI = 7;
    const STATUS_REDDEDILDI = 8;
    const STATUS_IADE_EDILDI = 9;
    const STATUS_IPTAL_EDILDI = 10;
    const STATUS_TAMAMLANDI = 11;


    public static function listStatus()
    {
        return [
            self::STATUS_BASARISIZ => 'Sipariş Başarısız',
            self::STATUS_GERI_ODEME => 'Sipariş Geri Ödeme Yapıldı',
            self::STATUS_ONAY_BEKLIYOR => 'Sipariş Onay Bekliyor',
            self::STATUS_SIPARIS_ALINDI => 'Sipariş Alındı',
            self::STATUS_HAZIRLANIYOR => 'Sipariş Hazırlanıyor',
            self::STATUS_HAZIRLANDI => 'Sipariş Hazırlandı',
            self::STATUS_KARGOYA_VERILDI => 'Sipariş Kargoya Verildi',
            self::STATUS_REDDEDILDI => 'Sipariş Reddedildi',
            self::STATUS_IADE_EDILDI => 'Sipariş İade Edildi',
            self::STATUS_IPTAL_EDILDI => 'Sipariş İptal Edildi',
            self::STATUS_TAMAMLANDI => 'Sipariş Tamamlandı',
        ];
    }

    public static function listStatusWithId()
    {
        return [
            self::STATUS_BASARISIZ => [SepetUrun::STATUS_BASARISIZ, SepetUrun::statusLabelStatic(self::STATUS_BASARISIZ)],
            self::STATUS_GERI_ODEME => [SepetUrun::STATUS_GERI_ODEME, SepetUrun::statusLabelStatic(self::STATUS_GERI_ODEME)],
            self::STATUS_ONAY_BEKLIYOR => [SepetUrun::STATUS_ONAY_BEKLIYOR, SepetUrun::statusLabelStatic(self::STATUS_ONAY_BEKLIYOR)],
            self::STATUS_SIPARIS_ALINDI => [SepetUrun::STATUS_SIPARIS_ALINDI, SepetUrun::statusLabelStatic(self::STATUS_SIPARIS_ALINDI)],
            self::STATUS_HAZIRLANIYOR => [SepetUrun::STATUS_HAZIRLANIYOR, SepetUrun::statusLabelStatic(self::STATUS_HAZIRLANIYOR)],
            self::STATUS_HAZIRLANDI => [SepetUrun::STATUS_HAZIRLANDI, SepetUrun::statusLabelStatic(self::STATUS_HAZIRLANDI)],
            self::STATUS_KARGOYA_VERILDI => [SepetUrun::STATUS_KARGOYA_VERILDI, SepetUrun::statusLabelStatic(self::STATUS_KARGOYA_VERILDI)],
            self::STATUS_REDDEDILDI => [SepetUrun::STATUS_REDDEDILDI, SepetUrun::statusLabelStatic(self::STATUS_REDDEDILDI)],
            self::STATUS_IADE_EDILDI => [SepetUrun::STATUS_IADE_EDILDI, SepetUrun::statusLabelStatic(self::STATUS_IADE_EDILDI)],
            self::STATUS_IPTAL_EDILDI => [SepetUrun::STATUS_IPTAL_EDILDI, SepetUrun::statusLabelStatic(self::STATUS_IPTAL_EDILDI)],
            self::STATUS_TAMAMLANDI => [SepetUrun::STATUS_TAMAMLANDI, SepetUrun::statusLabelStatic(self::STATUS_TAMAMLANDI)],

        ];
    }

    public static function statusLabelStatic($param)
    {
        $list = SepetUrun::listStatus();
        return $list[$param];
    }

    public function statusLabel()
    {
        $list = self::listStatus();
        return $list[$this->status];
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Urun');
    }
}
