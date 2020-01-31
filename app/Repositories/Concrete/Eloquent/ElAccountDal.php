<?php namespace App\Repositories\Concrete\Eloquent;

use App\Kullanici;
use App\Models\KullaniciAdres;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\AccountInterface;

class ElAccountDal implements AccountInterface
{

    protected $model;
    private $_kullaniciAddressRepository;

    public function __construct(Kullanici $model, KullaniciAdres $kullaniciAddressRepository)
    {
        $this->model = app()->makeWith(ElBaseRepository::class, ['model' => $model]);
        $this->_kullaniciAddressRepository = app()->makeWith(ElBaseRepository::class, ['model' => $kullaniciAddressRepository]);
    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        return $this->model->all($filter, $columns, $relations)->get();
    }

    public function allWithPagination($filter = null, $columns = array("*"), $perPageItem = null, $relations = null)
    {
        return $this->model->allWithPagination($filter, $columns, $perPageItem);
    }

    public function getById($id, $columns = array('*'), $relations = null)
    {
        return $this->model->getById($id, $columns, $relations);
    }

    public function getByColumn(string $field, $value, $columns = array('*'), $relations = null)
    {
        return $this->model->getByColumn($field, $value, $columns, $relations);
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
        return $this->model->delete($id);
    }


    public function with($relations, $filter = null, bool $paginate = null, int $perPageItem = null)
    {
        return $this->model->with($relations, $filter, $paginate, $perPageItem);
    }

    public function getUserAddress($userId, $addressType)
    {
        return $this->_kullaniciAddressRepository->all(['user' => $userId, 'type' => $addressType], null, ['City', 'Town'])->get();
    }

    public function getAddressById($addressId)
    {
        return KullaniciAdres::find($addressId);
    }

    public function getUserDefaultAddress($userId)
    {
        $user = Kullanici::find($userId);
        if ($user) {
            $defaultAddress = KullaniciAdres::with(['City', 'Town', 'User'])->find($user->detail->default_address);
            if (!is_null($defaultAddress))
                return $defaultAddress;
            return null;
        }
        return null;
    }

    public function setUserDefaultAddress($userId, $addressId)
    {
        $user = Kullanici::with('detail')->find($userId);
        if ($user) {
            $user->detail->default_address = intval($addressId);
            $user->detail->save();
            return true;
        }
        return false;
    }

    public function updateOrCreateUserAddress($id, $data, $userId)
    {
        $data['type'] = !isset($data['type']) ? 1 : $data['type'];
        $user = Kullanici::with('detail')->find($userId);
        if ($id != 0) {
            $adres = KullaniciAdres::find($id);
            $adres->update($data);
        } else {
            $adres = KullaniciAdres::create($data);
        }
        $userAllAddress = KullaniciAdres::where(['user' => $userId])->count();
        $typeIsSetDefaultValue = $data['type'] == 1 ? $user->detail->default_address : $user->detail->default_invoice_address;
        if ($userAllAddress == 1 || is_null($typeIsSetDefaultValue)) {
            if ($data['type'] == 1)
                $user->detail->default_address = $adres->id;
            else
                $user->detail->default_invoice_address = $adres->id;
            $user->detail->save();
        }
        return $adres;
    }

    public function getUserDefaultInvoiceAddress($userId)
    {
        $user = Kullanici::find($userId);
        if ($user) {
            $defaultInvoiceAddress = KullaniciAdres::with(['City', 'Town', 'User'])->find($user->detail->default_invoice_address);
            if (!is_null($defaultInvoiceAddress))
                return $defaultInvoiceAddress;
            return null;
        }
        return null;
    }

    public function setUserDefaultInvoiceAddress($userId, $addressId)
    {
        $user = Kullanici::with('detail')->find($userId);
        if ($user) {
            $user->detail->default_invoice_address = intval($addressId);
            $user->detail->save();
            return true;
        }
        return false;
    }
}
