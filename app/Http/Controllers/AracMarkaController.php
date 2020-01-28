<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\AracMarkaInterface;
use Illuminate\Http\Request;

class AracMarkaController extends Controller
{
    private $_aracMarkaService;

    public function __construct(AracMarkaInterface $aracMarkaService)
    {
        $this->_aracMarkaService = $aracMarkaService;
    }

    public function getAllActiveCarBrands()
    {
        $brands = $this->_aracMarkaService->all(['active' => 1]);
        return response()->json($brands);
    }

    public function getModelsByMarkaId($markaId)
    {
        return response()->json($this->_aracMarkaService->getModelsByMarkaId($markaId, 1));
    }
}
