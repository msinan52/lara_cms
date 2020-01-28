<?php namespace App\Repositories\Concrete\Eloquent;

use App\Models\City;
use App\Models\Log;
use App\Models\Town;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\CityTownInterface;

class ElCityTownDal implements CityTownInterface
{

    private $_cityService;
    private $_townService;

    public function __construct(City $city, Town $town)
    {
        $this->_cityService = app()->makeWith(ElBaseRepository::class, ['model' => $city]);
        $this->_townService = app()->makeWith(ElBaseRepository::class, ['model' => $town]);
    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        if (!is_null($relations))
            return City::with($relations)->where($filter)->orderBy('title', 'asc')->get();
        return City::when(!is_null($filter), function ($query) use ($filter) {
            $query->where($filter);
        })->orderBy('title', 'asc')->get();
    }

    public function allWithPagination($filter = null, $columns = array("*"), $perPageItem = null, $relations = null)
    {
        return $this->_cityService->allWithPagination($filter, $columns, $perPageItem);
    }

    public function getById($id, $columns = array('*'), $relations = null)
    {
        return $this->_cityService->getById($id, $columns, $relations);
    }

    public function getByColumn(string $field, $value, $columns = array('*'), $relations = null)
    {
        return $this->_cityService->getByColumn($field, $value, $columns, $relations);
    }

    public function create(array $data)
    {
        return $this->_cityService->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->_cityService->update($data, $id);
    }

    public function delete($id)
    {
        return $this->_cityService->delete();
    }


    public function with($relations, $filter = null, bool $paginate = null, int $perPageItem = null)
    {
        return $this->_cityService->with($relations, $filter, $paginate, $perPageItem);
    }

    public function getTownsByCityId($cityId)
    {
        return Town::where(['city' => $cityId, 'active' => 1])->get();
    }

}
