<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Repositories\Interfaces\FotoGalleryInterface;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    private $_galleryService;

    public function __construct(FotoGalleryInterface $galleryService)
    {
        $this->_galleryService = $galleryService;
    }

    public function list()
    {
        if (!config('admin.use_album_gallery')) {
            return redirect(route('gallery.edit', 0));
        }
        $list = $this->_galleryService->allWithPagination();
        return view('gallery.listGallery', compact('list'));
    }

    public function detail($slug)
    {
        $images = [];
        $item = new Gallery();
        if (!config('admin.use_album_gallery')) {
            $galleries = $this->_galleryService->all();
            if (count($galleries) > 0) {
                $item = $this->_galleryService->getById($galleries[count($galleries) - 1])[0];
                if ($item)
                    $images = $item->images;
            }
        } else {
            $item = $this->_galleryService->getByColumn('slug', $slug);
            if ($item)
                $images = $item->images;
        }
        return view('gallery.galleryDetail.blade', compact('item', 'images'));
    }
}
