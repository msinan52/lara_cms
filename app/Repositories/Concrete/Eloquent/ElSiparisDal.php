<?php namespace App\Repositories\Concrete\Eloquent;

use App\Models\Iyzico;
use App\Models\IyzicoFails;
use App\Models\İyzicoFailsJson;
use App\Models\SepetUrun;
use App\Models\Siparis;
use App\Models\Urun;
use App\Models\UrunSubAttribute;
use App\Models\UrunVariant;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\SiparisInterface;

class ElSiparisDal implements SiparisInterface
{
    protected $model;

    public function __construct(Siparis $model)
    {
        $this->model = app()->makeWith(ElBaseRepository::class, ['model' => $model]);
    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        // TODO: Implement all() method.
    }

    public function allWithPagination($filter = null, $columns = array("*"), $perPageItem = null, $relations = null)
    {
        // TODO: Implement allWithPagination() method.
    }

    public function getById($id, $columns = array('*'), $relations = null)
    {
        return $this->model->getById($id, $columns, $relations);
    }

    public function getByColumn(string $field, $value, $columns = array('*'), $relations = null)
    {
        // TODO: Implement getByColumn() method.
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->update($data, $id);
    }

    public function delete($id)
    {
        $order = $this->model->getById($id);
        return $order->forceDelete($id);
    }

    public function with($relations, $filter = null, bool $paginate = null, int $perPageItem = null)
    {
        return $this->model->with($relations, $filter, $paginate, $perPageItem);
    }

    public function orderFilterByStatusAndSearchText($search_text, $status, $paginate = false)
    {
        return $this->model->with('basket.user')
            ->when($status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->where(function ($query) use ($search_text) {
                return $query->where('full_name', 'like', "%$search_text%")
                    ->orWhere('id', $search_text);
            })
            ->orderByDesc('id')->when($paginate !== null, function ($q) use ($paginate) {
                if ($paginate === true)
                    return $q->paginate();
                return $q->get();
            });
    }

    public function getUserAllOrders(int $user_id)
    {
        return $this->model->with('basket')->whereHas('basket', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
    }

    public function getUserOrderDetailById(int $user_id, int $order_id)
    {
        return $this->model->with('basket.basket_items.product')->whereHas('basket', function ($query) use ($user_id, $order_id) {
            $query->where('user_id', $user_id);
        })->where('siparisler.id', $order_id)->firstOrFail();
    }

    public function updateOrderWithItemsStatus($order_id, $order_data, $order_items_status)
    {
        try {
            $data = $this->getById($order_id, null, 'basket.basket_items');
            $data->update($order_data);
            foreach ($order_items_status as $item) {
                SepetUrun::where(['id' => $item[0]])->first()->update(['status' => $item[1]]);
            }
            session()->flash('message', config('constants.messages.success_message'));

        } catch (\Exception $exception) {
            session()->flash('message', 'sepet güncellenirken hata oldu ' + $exception->getMessage());
            session()->flash('message_type', 'danger');
        }
    }

    public function decrementProductQty($productIdListQtyAndSelectedSubAttributesList, $qty = 1)
    {
        foreach ($productIdListQtyAndSelectedSubAttributesList as $key => $value) {
            $variant = UrunVariant::urunHasVariant($value[0], $value[1]);
            if ($variant != false)
                $variant->decrement('qty', $value[2]);
            else
                Urun::find($value[0])->decrement('qty', $value[2]);
        }
    }

    public function createOrderIyzicoDetail($iyzicoData, $orderId, $iyzicoJsonResponse)
    {
        $iyzicoData['siparis_id'] = $orderId;
        $iyzicoData['iyzicoJson'] = $iyzicoJsonResponse;
        $iyzicoOrder = Iyzico::create($iyzicoData);
        return $iyzicoOrder;
    }

    public function getIyzicoErrorLogs($query)
    {
        return İyzicoFailsJson::orderByDesc('id')->when(!is_null($query), function ($q) use ($query) {
            $q->where('user_id', 'like', "%$query%")
                ->orWhere('full_name', 'like', "%$query%")
                ->orWhere('basket_id', 'like', "%$query%")
                ->orWhere('json_response', 'like', "%$query%");
        })->simplePaginate();
    }

    public function getOrderIyzicoDetail($id)
    {
        return İyzicoFailsJson::find($id);
    }
}
