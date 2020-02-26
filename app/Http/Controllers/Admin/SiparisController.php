<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\OdemeController;
use App\Mail\OrderStatusOnChangedMail;
use App\Models\Ayar;
use App\Models\SepetUrun;
use App\Models\Siparis;
use App\Models\SiteOwnerModel;
use App\Repositories\Concrete\BaseRepository;
use App\Repositories\Interfaces\KuponInterface;
use App\Repositories\Interfaces\SiparisInterface;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SiparisController extends Controller
{
    protected $model;
    private $_kuponService;

    public function __construct(SiparisInterface $model, KuponInterface $kuponService)
    {
        $this->model = $model;
        $this->_kuponService = $kuponService;
    }


    public function list()
    {
        request()->validate(['status_filter' => 'numeric']);
        $query = request('q');
        $status_filter = request('status_filter');
        if ($query | $status_filter) {
            $list = $this->model->orderFilterByStatusAndSearchText($query, $status_filter, true);
        } else {
            $list = $this->model->with('basket.user', null, true);
        }
        $filter_types = Siparis::listStatusWithId();
        return view('admin.order.list_orders', compact('list', 'filter_types'));
    }

    public function newOrEditOrder($id)
    {
        $order = $this->model->with('basket.user')->findOrFail($id);
        if ($order->is_payment == 0) {
            session()->flash('message_type', 'danger');
            session()->flash('message', 'Dikkat bu işlem 3D security kısmını geçememiştir.Ödeme İşlemi gerçekleşmemiştir');
        }
        $order->iyzico->iyzicoJson = json_encode(json_decode($order->iyzico->iyzicoJson), JSON_PRETTY_PRINT);
        $filter_types = Siparis::listStatusWithId();
        $item_filter_types = SepetUrun::listStatusWithId();
        $basket_coupon = $this->_kuponService->getById($order->basket->coupon);
        return view('admin.order.new_edit_order', compact('order', 'filter_types', 'item_filter_types', 'basket_coupon'));
    }

    public function saveOrder($id)
    {
        $status = \request('status');
        $order = $this->model->getById($id, null, ['basket.user', 'basket.basket_items']);
        $order_items = array();
        foreach ($order->basket->basket_items as $item) {
            array_push($order_items, [$item->id, \request()->get("orderItem$item->id")]);
        }
        $this->model->updateOrderWithItemsStatus($id, ['status' => $status], $order_items);

        if ($status != $order->status) {
            $prices = $this->calculateOrderSubTotalCargoCouponAndTotal($order->basket, $order);
            \Mail::to($order->basket->user->email)->send(new OrderStatusOnChangedMail($order->basket->user, $order, $order->basket->basket_items, $prices, Siparis::statusLabelStatic(\request('status'))));
        }
        if ($order)
            return redirect(route('admin.order.edit', $order->id));
        return back()->withInput();
    }

    private function calculateOrderSubTotalCargoCouponAndTotal($currentBasket, $order)
    {
        $basketCoupon = null;
        $cartSubTotalPrice = $order->order_price;
        if (!is_null($currentBasket->coupon)) {
            $basketCoupon = $this->_kuponService->getById($currentBasket->coupon);
        }
        $cargoPrice = $order->cargo_price;
        $cartTotalPrice = $cartSubTotalPrice + $cargoPrice;
        if (!is_null($basketCoupon)) {
            $cartTotalPrice -= $basketCoupon->discount_price;
        }
        return [
            'cartSubTotalPrice' => $cartSubTotalPrice,
            'basketCoupon' => $basketCoupon,
            'cargoPrice' => $cargoPrice,
            'cartTotalPrice' => $cartTotalPrice,
        ];
    }

    public function deleteOrder($id)
    {
        $this->model->delete($id);
        return redirect(route('admin.orders'));
    }

    public function iyzicoErrorOrderList()
    {
        $query = request('q');
        $list = $this->model->getIyzicoErrorLogs($query);
        return view('admin.order.listIyzicoFails', compact('list'));
    }

    public function iyzicoErrorOrderDetail($id)
    {
        $json = $this->model->getOrderIyzicoDetail($id)->json_response;
        $json = json_decode($json,true);
        return $json;
    }

    public function invoiceDetail($id)
    {
        $order = $this->model->with(['basket.user', 'iyzico', 'basket.basket_items.product'])->findOrFail($id);
        $site = Ayar::getCache();
        $owner = SiteOwnerModel::getLast();
        $basket_coupon = $this->_kuponService->getById($order->basket->coupon);
        return view('site.siparis.invoice.invoiceDetail', compact('order', 'site', 'owner', 'basket_coupon'));
    }

}
