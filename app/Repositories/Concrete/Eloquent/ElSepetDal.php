<?php namespace App\Repositories\Concrete\Eloquent;

use App\Models\Sepet;
use App\Models\SepetUrun;
use App\Models\Urun;
use App\Models\UrunVariant;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\SepetInterface;
use Gloudemans\Shoppingcart\Facades\Cart;

class ElSepetDal implements SepetInterface
{

    protected $model;

    public function __construct(Sepet $model)
    {
        $this->model = app()->makeWith(ElBaseRepository::class, ['model' => $model]);
    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        return $this->model->all($filter, $columns, $relations)->get();
    }

    public function allWithPagination($filter = null, $columns = array("*"), $perPageItem = null, $relations = null)
    {
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

    /**
     *  Parametre olarak gönderilen $checkedQty $subAttributesIdList göre Ürün varyantlarında stok durumu kontrol edilir
     * sonra kullanıcının sepetinde bu üründen kaç adet var ve stokda kaç tane var kontrol edilir
     * eğer $checkedQty $maxQty den büyükse   $checkedQty = $maxQty  değeri atanır ve max o adet kadar ekleyebilir veya silebilir
     * kullanıcı sepete eklediği ürün sayısınndan az bir sayı gönderdiyse eksi olarak geri döner örn 4 ürün varken 3 gönderirse -1 döner
     * @param $productId   Ürün id
     * @param $checkedQty kontrol edilecek ürün adedi
     * @param $subAttributesIdList ürün varyant alt özellik id leri
     * @return $checkedQty
     */
    public function checkProductQtyCountCanAddToBasketItemCount($productId, $qty, $subAttributesIdList = null)
    {
        $variant = UrunVariant::urunHasVariant($productId, $subAttributesIdList);
        $product = Urun::findOrFail($productId);
        $maxQty = $product->qty;
        if ($variant !== false) {
            $maxQty = $variant->qty;
        }
        $search = Cart::search(function ($key, $value) use ($product, $subAttributesIdList) {
            return $key->id === $product->id && $key->options->selectedSubAttributesIdList == $subAttributesIdList;
        })->first();
        !is_null($search) ?: null;
        if (!is_null($search)) {
            $maxQty = $maxQty - $search->qty;
            $qty != 0 ? $qty -= $search->qty : null;
        }
        if ($qty > $maxQty) {
            $qty = $maxQty;
        }
        return $qty;
    }

    public function updateBasketQty($qty, $cartItemRowId)
    {
        $cartItem = Cart::get($cartItemRowId);
        $product = Urun::with('variants')->findOrFail($cartItem->id);
        $canAddQtyItemOnBasketCount = $this->checkProductQtyCountCanAddToBasketItemCount($product->id, $qty, $cartItem->options->selectedSubAttributesIdList);

        if ($canAddQtyItemOnBasketCount > 0 || $canAddQtyItemOnBasketCount < 0) {
            $newQty = $cartItem->qty + $canAddQtyItemOnBasketCount;
            if ($newQty > 0) {
                Cart::update($cartItemRowId, $newQty);
                if (auth()->check()) {
                    $current_basket_id = Sepet::getCreate_current_basket_id(false);
                    if (isset($current_basket_id) && $current_basket_id != null) {
                        SepetUrun::where(['product_id' => $cartItem->id, 'sepet_id' => $current_basket_id, 'attributes_text' => $cartItem->options->attributeText])->update(['qty' => $newQty]);
                        session()->flash('message', 'Adet bilgisi güncellendi');
                    }
                }
            }
        }
        return $canAddQtyItemOnBasketCount;


    }
}
