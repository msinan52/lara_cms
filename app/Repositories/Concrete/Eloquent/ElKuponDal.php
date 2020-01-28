<?php namespace App\Repositories\Concrete\Eloquent;

use App\Jobs\DeleteAllLogsJobs;
use App\Models\Banner;
use App\Models\Coupon;
use App\Models\KategoriUrun;
use App\Models\Log;
use App\Models\Urun;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\BannerInterface;
use App\Repositories\Interfaces\KuponInterface;
use App\Repositories\Interfaces\LogInterface;
use Carbon\Carbon;
use Intervention\Image\ImageManagerStatic as Image;

class ElKuponDal implements KuponInterface
{

    protected $model;

    public function __construct(Coupon $model)
    {
        $this->model = app()->makeWith(ElBaseRepository::class, ['model' => $model]);
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

    public function checkCoupon($cartItems, $couponCode, $cartSubTotalPrice, $basket)
    {
        $curDate = date('Y-m-d H:i:s');
        $message = null;
        $status = true;
        $coupon = Coupon::where([
            ['active', '=', 1],
            ['code', '=', $couponCode],
            ['start_date', '<=', $curDate],
            ['end_date', '>=', $curDate],
        ])->first();
        if (!is_null($coupon)) {
            $productIdList = $cartItems->pluck('id');
            $productCategoryIdList = KategoriUrun::whereIn('product_id', $productIdList)->pluck('category_id');
            $couponCategoryIdList = $coupon->categories->pluck('id');
            $couponCategoryTitleList = $coupon->categories->pluck('slug')->map(function ($name) {
                return strtoupper(str_replace('-', " ", $name));
            });
            $hasDifferentCategories = $productCategoryIdList->diff($couponCategoryIdList)->all();
            if ($coupon->qty <= 0) {
                $message = "Üzgünüz $couponCode kuponu tükendi";
                $status = false;
            } else if ($cartSubTotalPrice <= $coupon->min_basket_price) {
                $message = "$couponCode kuponu uygulamak için sepetinizde minimum $coupon->min_basket_price TL değerinde ürün olmalıdır";
                $status = false;
            } else if (count($hasDifferentCategories) > 0) {
                $message = "$couponCode kuponu sadece {$couponCategoryTitleList} kategorilerinde geçerlidir.kuponu kullanmak için diğer kategorilerdeki ürünleri sepetten çıkarmalısınız";
                $status = false;
            } else {
                $basket->coupon = $coupon->id;
                $basket->save();
            }
        } else {
            $message = "kupon bulunamadı";
            $status = false;
        }
        if ($status == false && !is_null($coupon)) {
            if ($basket->coupon == $coupon->id) {
                $basket->coupon = null;
                $basket->save();
            }
        }
        $data = [
            'message' => !is_null($message) ? $message : "Kupon başarıyla eklendi",
            'status' => $status
        ];
        return $data;
    }

    public function decrementCouponQty($couponId)
    {
        $coupon = $this->getById($couponId);
        if ($coupon) {
            $coupon->decrement('qty', 1);
            return $coupon;
        }
        return null;
    }
}
