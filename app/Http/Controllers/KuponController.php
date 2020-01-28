<?php

namespace App\Http\Controllers;

use App\Models\Sepet;
use App\Repositories\Interfaces\KuponInterface;
use Gloudemans\Shoppingcart\Facades\Cart;

class KuponController extends Controller
{
    protected $model;

    public function __construct(KuponInterface $model)
    {
        $this->model = $model;
    }

    public function applyCoupon()
    {
        $code = \request()->get('code');
        $basketItems = Cart::content();
        $basketId = Sepet::getCreate_current_basket_id(false);
        $result = $this->model->checkCoupon($basketItems, $code, Cart::subTotal(), Sepet::find($basketId));
        return redirect(route('basketView'))->with('message', $result['message'])->with('message_type', $result['status'] == true ? 'success' : 'danger');
    }
}
