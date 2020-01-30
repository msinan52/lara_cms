<?php

namespace App;

use App\Models\Auth\Role;
use App\Models\KullaniciAdres;
use App\Models\Log;
use App\Notifications\PasswordReset;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Kullanici extends Authenticatable
{

    use SoftDeletes, Notifiable;
    protected $table = "kullanicilar";
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'activation_code', 'is_active', 'is_admin'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    protected $hidden = [
        'password', 'activation_code',
    ];

    public function getFullName()
    {
        return $this->name . " " . $this->surname;
    }

    public function detail()
    {
        return $this->hasOne('App\Models\KullaniciDetay', 'user_id', 'id')->withDefault();
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }
}
