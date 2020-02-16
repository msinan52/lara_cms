<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'icerik_yonetim';
    protected $perPage = 20;
    protected $guarded = [];

    public function parentContent()
    {
        return $this->belongsTo(Content::class, 'parent', 'id');
    }
}
