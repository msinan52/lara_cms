<?php namespace App\Repositories\Concrete\AnotherOrm;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\RepositoryInterface;

class MSOrderRepository implements OrderRepositoryInterface
{

    function getAll()
    {
        return 'MS ORM RETURNED';
    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        return "bu all metodu MS orm den dömüştür";
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
        // TODO: Implement with() method.
    }
}
