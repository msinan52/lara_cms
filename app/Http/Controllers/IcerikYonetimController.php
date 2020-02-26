<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\IcerikYonetimInterface;
use Illuminate\Http\Request;

class IcerikYonetimController extends Controller
{
    private $_icerikYonetimService;

    public function __construct(IcerikYonetimInterface $icerikYonetimService)
    {
        $this->_icerikYonetimService = $icerikYonetimService;
    }

    public function detail($slug)
    {
        $item = $this->_icerikYonetimService->getByColumn('slug', $slug);
        if (is_null($item))
            return redirect(route('homeView'))->withErrors('içerik bulunamadı');
        return view('site.icerik.contentDetail', compact('item'));
    }
}
