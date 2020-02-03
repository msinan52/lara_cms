<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\ReferenceInterface;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    private $_referenceService;

    public function __construct(ReferenceInterface $referenceService)
    {
        $this->_referenceService = $referenceService;
    }

    public function list()
    {
        $list = $this->_referenceService->allWithPagination();
        return view('site.referans.listReferences', compact('list'));
    }

    public function detail($slug)
    {
        $item = $this->_referenceService->getByColumn('slug', $slug);
        if (is_null($item))
            return redirect(route('homeView'))->withErrors('referans bulunamadı');
        return view('site.referans.referenceDetail', compact('item'));
    }

}
