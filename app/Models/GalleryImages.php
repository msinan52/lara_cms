<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImages extends Model
{
    public $timestamps = false;
    protected $table = 'gallery_images';
    protected $perPage = 20;
    protected $guarded = [];

    const  IMAGE_QUALITY = 50;
    const  IMAGE_RESIZE = null;

    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'gallery_id', 'id');
    }
}
