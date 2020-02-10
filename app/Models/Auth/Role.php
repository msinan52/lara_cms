<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $guarded = [];
    protected $perPage = 10;

    public function permissions()
    {
        return $this->belongsToMany('App\Models\Auth\Permission')->orderBy('name');
    }

    public function users()
    {
        return $this->hasMany('App\Models\Auth\User');
    }
}
