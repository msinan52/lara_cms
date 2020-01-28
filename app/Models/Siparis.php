<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siparis extends Model
{
    use  SoftDeletes;
    protected $table = "siparisler";
    protected $perPage = 20;

    protected $fillable = [
        'sepet_id', 'order_price', 'status',
        'full_name', 'adres', 'phone', 'bank', 'installment_count', 'fatura_adres', 'cargo_price', 'order_total_price', 'ip_adres'
    ];

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
    const STATUS_ODEME_ALINAMADI = 12;


    public function scopeGetOrderCountByStatus($query, $status_type)
    {
        return $query->where('status', $status_type)->count();
    }


    public function statusLabel()
    {
        $list = self::listStatus();
        return $list[$this->status];
    }

    public static function statusLabelStatic($param)
    {
        $list = Siparis::listStatus();
        return $list[$param];
    }


    public function basket()
    {
        return $this->belongsTo('App\Models\Sepet', 'sepet_id');
    }

    public function iyzico()
    {
        return $this->hasOne(Iyzico::class, 'siparis_id', 'id')->withDefault();
    }


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
            self::STATUS_ODEME_ALINAMADI => 'Ödeme İşlemi Sırasında hata oluştu'
        ];
    }

    public static function listStatusWithId()
    {
        return [
            self::STATUS_BASARISIZ => [Siparis::STATUS_BASARISIZ, Siparis::statusLabelStatic(self::STATUS_BASARISIZ)],
            self::STATUS_GERI_ODEME => [Siparis::STATUS_GERI_ODEME, Siparis::statusLabelStatic(self::STATUS_GERI_ODEME)],
            self::STATUS_ONAY_BEKLIYOR => [Siparis::STATUS_ONAY_BEKLIYOR, Siparis::statusLabelStatic(self::STATUS_ONAY_BEKLIYOR)],
            self::STATUS_SIPARIS_ALINDI => [Siparis::STATUS_SIPARIS_ALINDI, Siparis::statusLabelStatic(self::STATUS_SIPARIS_ALINDI)],
            self::STATUS_HAZIRLANIYOR => [Siparis::STATUS_HAZIRLANIYOR, Siparis::statusLabelStatic(self::STATUS_HAZIRLANIYOR)],
            self::STATUS_HAZIRLANDI => [Siparis::STATUS_HAZIRLANDI, Siparis::statusLabelStatic(self::STATUS_HAZIRLANDI)],
            self::STATUS_KARGOYA_VERILDI => [Siparis::STATUS_KARGOYA_VERILDI, Siparis::statusLabelStatic(self::STATUS_KARGOYA_VERILDI)],
            self::STATUS_REDDEDILDI => [Siparis::STATUS_REDDEDILDI, Siparis::statusLabelStatic(self::STATUS_REDDEDILDI)],
            self::STATUS_IADE_EDILDI => [Siparis::STATUS_IADE_EDILDI, Siparis::statusLabelStatic(self::STATUS_IADE_EDILDI)],
            self::STATUS_IPTAL_EDILDI => [Siparis::STATUS_IPTAL_EDILDI, Siparis::statusLabelStatic(self::STATUS_IPTAL_EDILDI)],
            self::STATUS_TAMAMLANDI => [Siparis::STATUS_TAMAMLANDI, Siparis::statusLabelStatic(self::STATUS_TAMAMLANDI)],
            self::STATUS_ODEME_ALINAMADI => [Siparis::STATUS_ODEME_ALINAMADI, Siparis::statusLabelStatic(self::STATUS_ODEME_ALINAMADI)],

        ];
    }

    public function calcOrderTotalPriceWithKDV()
    {
        return (($this->order_price * config('cart.tax')) / 100) + $this->order_price;
    }


}
