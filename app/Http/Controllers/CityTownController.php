<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\CityTownInterface;

class CityTownController extends Controller
{
    protected $model;

    public function __construct(CityTownInterface $model)
    {
        $this->model = $model;
    }

    public function getTownsByCityId($cityId)
    {
        return $this->model->getTownsByCityId($cityId);
    }

}
