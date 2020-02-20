<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    public $timestamps = true;
    protected $table = 'gallery';
    protected $guarded = [];
    protected $perPage = 10;
    const  IMAGE_QUALITY = 90;
    const  IMAGE_RESIZE = null;


    public function images()
    {
        return $this->hasMany(GalleryImages::class, 'gallery_id', 'id')->orderByDesc('id');
    }

    public function imagesCount()
    {
        return $this->hasMany(GalleryImages::class, 'gallery_id', 'id')->count();
    }
}
