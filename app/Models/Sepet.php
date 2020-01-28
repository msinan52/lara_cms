<?php

namespace App\Models;

use App\Http\Requests\UpdateBasketQtyRequest;
use App\Kullanici;
use App\Repositories\Concrete\Eloquent\ElSepetDal;
use App\Repositories\Interfaces\SepetInterface;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Sepet extends Model
{
    use SoftDeletes;
    protected $table = 'sepet';
    protected $guarded = [];


    public static function addItemToBasket($product, $attributeText = null, $selectedSubAttributesIdList = null, $qty = 1)
    {
        $message = "";
        $oldPrice = null;
        try {
            $maxQty = $product->qty;
            if (!is_null($product->discount_price)) {
                $oldPrice = $product->price;
                $product->price = $product->discount_price;
            }
            if (!is_null($selectedSubAttributesIdList)) {
                $variant = UrunVariant::urunHasVariant($product->id, $selectedSubAttributesIdList);
                if ($variant !== false) {
                    $product->price = $variant->price;
                    $maxQty = $variant->qty;
                }
            }
            $search = Cart::search(function ($key, $value) use ($product, $selectedSubAttributesIdList) {
                return $key->id === $product->id && $key->options->selectedSubAttributesIdList == $selectedSubAttributesIdList;
            })->first();
            !is_null($search) ?: null;
            if (!is_null($search))
                $maxQty = $maxQty - $search->qty;
            if ($qty >= $maxQty)
                $qty = $maxQty;
            if ($qty > 0) {
                $cartItem = Cart::add($product->id, $product->title, $qty, $product->price, ['slug' => $product->slug, 'image' => $product->image, 'attributeText' => $attributeText, 'selectedSubAttributesIdList' => $selectedSubAttributesIdList, 'old_price' => $oldPrice]);
                if (auth()->check()) {
                    $current_basket_id = Sepet::getCreate_current_basket_id();
                    SepetUrun::updateOrCreate(
                        ['sepet_id' => $current_basket_id, 'product_id' => $product->id, 'attributes_text' => $attributeText], ['qty' => $cartItem->qty, 'price' => $product->price, 'status' => SepetUrun::STATUS_ONAY_BEKLIYOR, 'attributes_text' => $attributeText]
                    );
                }
                $status = true;
            } else {
                $message = "Yetersiz Stok";
                $status = false;
            }
            return ['message' => $message, 'status' => $status];
        } catch (\Exception $exception) {
            return ['message' => $message, 'status' => "Ürün sepete eklenirken bir hata oluştu."];
        }
    }

    public static function removeItemFromBasket($cartItemRowId)
    {
        try {
            $cartItem = Cart::get($cartItemRowId);
            Cart::remove($cartItem->rowId);
            if (auth()->check()) {
                $current_basket_id = Sepet::getCreate_current_basket_id(false);
                if (!is_null($current_basket_id)) {
                    $selected_basket_item = SepetUrun::where(['product_id' => $cartItem->id, 'sepet_id' => $current_basket_id, 'attributes_text' => $cartItem->options->attributeText])->get()->first();
                    $selected_basket_item->delete();
                }
            }
            return true;
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
            session()->flash('message_type', 'danger');
            return false;
        }
    }

    public static function clearAllBasketItems()
    {
        Cart::destroy();
        if (auth()->check()) {
            $current_basket_id = session('current_basket_id');
            if (!is_null($current_basket_id)) {
                SepetUrun::where('sepet_id', $current_basket_id)->delete();
            }
        }
    }

    public function updateBasketQty($qty, $cartItemRowId)
    {
        $validator = Validator::make(request()->all(), [
            'qty' => 'required|numeric|between:0,10'
        ]);
        if ($validator->fails()) {
            $fails = Str::ascii($validator->messages());
            session()->flash('message', $fails);
            session()->flash('message_type', 'danger');
            return response()->json(['success' => false]);
        }
        $sepetService = new ElSepetDal(new Sepet());
        return $sepetService->updateBasketQty($qty, $cartItemRowId);
    }

    public function order()
    {
        return $this->hasOne('App\Models\Siparis');
    }

    public static function getCreate_current_basket_id($canCreateNewBasket = true)
    {
        $current_basket = DB::table('sepet as s')
            ->leftJoin('siparisler as si', 'si.sepet_id', '=', 's.id')
            ->where('s.user_id', auth()->id())
            ->whereRaw('si.id is null')
            ->orderByDesc('s.created_at')
            ->select('s.id')
            ->first();
        if ($canCreateNewBasket == false && is_null($current_basket))
            return null;
        if (is_null($current_basket) && $canCreateNewBasket)  // create new basket
            $current_basket = Sepet::create(['user_id' => auth()->id()]);
        session()->put('current_basket_id', $current_basket->id);
        return $current_basket->id;
    }

    public function get_basket_item_count()
    {
        return $this->hasMany('App\Models\SepetUrun')->where('sepet_id', $this->id)->sum('qty');
    }

    public function basket_items()
    {
        return $this->hasMany('App\Models\SepetUrun');
    }


    public function user()
    {
        return $this->belongsTo(Kullanici::class, 'user_id');
    }


}
