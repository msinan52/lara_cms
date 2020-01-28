<?php

namespace App\Http\Controllers;

use App\Models\Ayar;
use App\Models\Coupon;
use App\Models\Sepet;
use App\Models\Urun;
use App\Repositories\Interfaces\KuponInterface;
use App\Repositories\Interfaces\SepetInterface;
use App\Repositories\Interfaces\UrunlerInterface;
use Gloudemans\Shoppingcart\Facades\Cart;
use mysql_xdevapi\Exception;
use function GuzzleHttp\Psr7\str;

class SepetController extends Controller
{
    protected $model;
    private $_productService;
    private $_kuponService;

    public function __construct(SepetInterface $model, UrunlerInterface $productService, KuponInterface $kuponService)
    {
        $this->model = $model;
        $this->_productService = $productService;
        $this->_kuponService = $kuponService;
    }

    public function index()
    {
        $basket = $basketCoupon = null;
        $cartSubTotalPrice = Cart::subTotal();
        $basketCouponPrice = 0;
        $cargoPrice = !is_null(Ayar::getCache()) ? Ayar::getCache()->cargo_price : config('constants.cargo_price');
        if (auth()->check()) {
            $basket = $this->model->getById(Sepet::getCreate_current_basket_id());
            if (!is_null($basket->coupon)) {
                $basketCoupon = $this->_kuponService->getById($basket->coupon);
                if (!is_null($basketCoupon)) {
                    $data = $this->_kuponService->checkCoupon(Cart::content(), $basketCoupon->code, $cartSubTotalPrice, $basket);
                    if ($data['status'] == false)
                        return redirect(route('basketView'))->with('message', $data['message'])->with('message_type', 'danger');
                    $basketCouponPrice = $basketCoupon->discount_price;
                }
            }
        }
        $totalPrice = $cartSubTotalPrice - $basketCouponPrice + $cargoPrice;
        return view('site.sepet.sepet', compact('basket', 'basketCoupon', 'totalPrice', 'cargoPrice'));
    }

    public function itemAddToBasket()
    {
        $product = $this->_productService->getById(request('id'));

        $index = 0;
        $attributeText = "";
        $selectedSubAttributesIdList = array();
        do {
            if (request()->has("attributeTitle$index")) {
                $attributeText .= $index != 0 ? '  ' . request("attributeTitle$index") : '' . request("attributeTitle$index");
                if (request()->has("subAttributeTitle$index")) {
                    $attributeText .= ' : ';
                    $attributeText .= request("subAttributeTitle$index") == null ? '-' : explode("|", request()->get("subAttributeTitle$index"))[1];
                    array_push($selectedSubAttributesIdList, explode("|", request()->get("subAttributeTitle$index"))[0]);
                }
            }
            $index++;
        } while ($index < 10);
        Sepet::addItemToBasket($product, $attributeText, $selectedSubAttributesIdList);
        return redirect()->route('basketView')->with('message', 'Ürün sepete eklendi.');
    }

    public function itemAddToBasketWithAjax()
    {
        $product = $this->_productService->getById(request('id'));
        $attributeText = "";
        $selectedSubAttributesIdTitleList = request()->get("selectedAttributeIdList");
        $subAttributesId = [];
        $qty = request()->get('qty', 1);
        if (is_array($selectedSubAttributesIdTitleList)) {
            foreach ($selectedSubAttributesIdTitleList as $index => $item) {
                $itemId = explode("|", $item)[0];
                $itemSubTitle = explode("|", $item)[1];
                $itemAttributeTitle = explode("|", $item)[2];
                $attributeText .= $itemAttributeTitle . " : " . $itemSubTitle . ' ';
                array_push($subAttributesId, $itemId);
            }
        }
        $status = Sepet::addItemToBasket($product, $attributeText, $subAttributesId, $qty);
        $data = [
            'status' => $status,
            'card' => Cart::content(),
            'cardPrice' => Cart::subTotal()
        ];
        return response()->json($data);
    }

    public function updateMultipleBasketItem()
    {
        $dataItemRowIdAndQty = request()->get("dataItemRowIdAndQty");
        $itemAdded = [];
        $couponPrice = 0;
        if (is_array($dataItemRowIdAndQty)) {
            foreach ($dataItemRowIdAndQty as $item) {
                $itemAddedStatus = $this->model->updateBasketQty($item[1], $item[0]);
                array_push($itemAdded, $itemAddedStatus);
            }
        }
        $cargoPrice = !is_null(Ayar::getCache()) ? Ayar::getCache()->cargo_price : config('constants.cargo_price');
        $cardSubTotal = Cart::subTotal();
        if (auth()->check()) {
            $currentBasket = $this->model->getById(session()->get('current_basket_id'));
            $coupon = $this->_kuponService->getById($currentBasket->coupon);
            $couponPrice = !is_null($coupon) ? $coupon->discount_price : 0;
        }
        $data = [
            'card' => Cart::content(),
            'cardPrice' => $cardSubTotal,
            'cardTotalPrice' => $cardSubTotal + $cargoPrice - $couponPrice,
        ];
        return response()->json($data);
    }


    public function removeItemFromBasket($rowId)
    {
        Sepet::removeItemFromBasket($rowId);
        return redirect()->route('basketView');
    }

    public function removeItemFromBasketWithAjax()
    {
        $rowId = request()->get('rowId');
        $status = Sepet::removeItemFromBasket($rowId);
        $data = [
            'status' => $status,
            'card' => Cart::content(),
            'cardPrice' => Cart::subTotal()
        ];
        return response()->json($data);

    }

    public function removeAllItems()
    {
        Sepet::clearAllBasketItems();
        return redirect()->route('basketView')->with('message', 'Sepetteki bütün ürünler silindi');
    }

    // ajax view
    public function updateBasket($rowId)
    {
        $basket = new Sepet();
        $result = $basket->updateBasketQty(request('qty'), $rowId);
        return response()->json(['status' => $result]);
    }
}
