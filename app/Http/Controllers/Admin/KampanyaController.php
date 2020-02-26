<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kampanya;
use App\Repositories\Interfaces\KampanyaInterface;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\KategoriInterface;
use App\Repositories\Interfaces\UrunFirmaInterface;

class KampanyaController extends Controller
{
    protected $model;
    private $_kategoriService;
    private $_companyService;

    public function __construct(KampanyaInterface $model, KategoriInterface $kategoriService, UrunFirmaInterface $companyService)
    {
        $this->model = $model;
        $this->_kategoriService = $kategoriService;
        $this->_companyService = $companyService;
    }

    public function list()
    {
        $query = request('q');
        if ($query) {
            $list = $this->model->allWithPagination([['title', 'like', "%$query%"]]);
        } else {
            $list = $this->model->allWithPagination();
        }
        return view('admin.campaign.listCampaigns', compact('list'));
    }

    public function newOrEditForm($id = 0)
    {
        $categories = $this->_kategoriService->all(['active' => 1]);
        $companies = $this->_companyService->all(['active' => 1]);
        $entry = new Kampanya();
        $selected_categories = $selected_products = $selected_companies = [];
        if ($id != 0) {
            $entry = $this->model->getById($id, null, ['campaignProducts', 'campaignCategories', 'campaignCompanies']);
            $selected_products = $entry->campaignProducts;
            $selected_categories = $entry->campaignCategories->pluck('id');
            $selected_companies = $entry->campaignCompanies->pluck('id');
        }
        return view('admin.campaign.newOrEditCampaign', compact('entry', 'categories', 'selected_categories', 'selected_products', 'companies', 'selected_companies'));
    }

    public function save($id = 0)
    {
        $request_data = \request()->only('title', 'discount_type', 'image', 'discount_amount', 'start_date', 'end_date', 'min_price', 'spot');
        $request_data['active'] = request()->has('active') ? 1 : 0;
        $posted_categories = request('categories');
        $posted_products = request('products');
        $posted_companies = request('companies');
        $request_data['slug'] = str_slug(request('title'));
        $request_data['selected_products'] = $posted_products;
        $request_data['selected_categories'] = $posted_categories;
        $request_data['selected_companies'] = $posted_companies;
        if ($this->model->all([['slug', $request_data['slug']], ['id', '!=', $id]], ['id'])->count() > 0) {
            return back()->withInput()->withErrors('slug alanı zaten kayıtlı');
        }
        if ($id != 0) {
            $entry = $this->model->update($request_data, $id);
        } else {
            $entry = $this->model->create($request_data);
        }
        if (request()->hasFile('image') && $entry) {
            $this->validate(request(), [
                'image' => 'image|mimes:jpg,png,jpeg,gif|max:' . config('admin.max_upload_size')
            ]);
            $this->model->uploadCampaignImage($entry, request()->file('image'));
        }
        Kampanya::forgetCaches();
        if ($entry)
            return redirect(route('admin.campaigns.edit', $entry->id));
        return back()->withInput();

    }

    public function delete($id)
    {
        $this->model->delete($id);
        Kampanya::forgetCaches();
        return redirect(route('admin.campaigns'));
    }
}
