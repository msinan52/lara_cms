<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use App\Models\Kategori;
use App\Repositories\Interfaces\BannerInterface;
use App\Repositories\Interfaces\KategoriInterface;
use App\Repositories\Interfaces\KuponInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KuponController extends Controller
{
    protected $model;
    protected $_kategoriService;

    public function __construct(KuponInterface $model, KategoriInterface $kategoriService)
    {
        $this->model = $model;
        $this->_kategoriService = $kategoriService;
    }

    public function list()
    {
        $query = \request()->get('q', null);
        $list = $this->model->allWithPagination([['code', 'like', "%$query%"]]);
        return view('admin.coupon.listCoupons', compact('list'));
    }

    public function newOrEditForm($id = 0)
    {
        $entry = new Coupon();
        if ($id != 0) {
            $entry = $this->model->getById($id);
        }
        $categories = $this->_kategoriService->all(['active' => 1]);
        $selected_categories = [];
        if ($id != 0) {
            $coupon = $this->model->getById($id, null, ['categories']);
            $selected_categories = $coupon->categories()->pluck('category_id')->all();
        }
        return view('admin.coupon.newOrEditCoupon', compact('entry', 'categories', 'selected_categories'));
    }

    public function save($id = 0)
    {
        $request_data = \request()->only('code', 'start_date', 'end_date', 'qty', 'discount_price', 'min_basket_price');
        $request_data['active'] = request()->has('active') ? 1 : 0;
        if ($this->model->all([['code', \request('code')], ['id', '!=', $id]], ['id'])->count() > 0) {
            return back()->withInput()->withErrors('aynı kod ile zaten kupon var');
        }
        if ($id != 0) {
            $this->model->update($request_data, $id);
            $entry = $this->model->getById($id, null, ['categories']);
        } else {
            $entry = $this->model->create($request_data);
        }
        $entry->categories()->sync(\request('categories'));
        if ($entry)
            return redirect(route('admin.coupons.edit', $entry->id));
        return back()->withInput();
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect(route('admin.coupons'));
    }
}
