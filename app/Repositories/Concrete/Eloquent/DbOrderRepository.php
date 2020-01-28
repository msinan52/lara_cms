<?php namespace App\Repositories\Concrete\Eloquent;

use App\Models\Ayar;
use App\Models\Kategori;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\KategoriInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;

class DbOrderRepository implements KategoriInterface
{
    protected $model;

    public function __construct(Kategori $model, BaseRepositoryInterface $repository)
    {
        $repository->setModel($model);
        $this->model = $repository;
    }

    function getAll()
    {
        return $this->all();
    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        return response()->json(['data'=>'merhaba farklı']);
    }

    public function allWithPagination($filter = null, $columns = array("*"), $perPageItem = null)
    {
        // TODO: Implement allWithPagination() method.
    }

    public function getById($id, $columns = array('*'), $relations = null)
    {
        // TODO: Implement getById() method.
    }

    public function getByColumn(string $field, $value, $columns = array('*'), $relations = null)
    {
        // TODO: Implement getByColumn() method.
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function with($relations, $filter = null, bool $paginate = null, int $perPageItem = null)
    {
        return array(['list' => [1, 2, 3]]);
    }
}
