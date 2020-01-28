<?php

namespace App\Http\Controllers;

use App\Models\Siparis;
use App\Repositories\Concrete\BaseRepository;
use App\Repositories\Interfaces\KuponInterface;
use App\Repositories\Interfaces\SiparisInterface;
use Illuminate\Http\Request;

class SiparisController extends Controller
{
    protected $model;
    private $_kuponService;

    public function __construct(SiparisInterface $model, KuponInterface $kuponService)
    {
        $this->model = $model;
        $this->_kuponService = $kuponService;
    }

    public function index()
    {
        $orders = $this->model->getUserAllOrders(auth()->id());
        return view('site.siparis.siparisler', compact('orders'));
    }

    public function siparisDetay($id)
    {
        $order = $this->model->getUserOrderDetailById(auth()->id(), $id);
        $basketCoupon = $order->basket;
        if (!is_null($basketCoupon)) {
            $basketCoupon = $this->_kuponService->getById($basketCoupon->coupon);
        }
        return view('site.siparis.siparisDetay', compact('order', 'basketCoupon'));
    }
}
