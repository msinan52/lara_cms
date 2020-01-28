<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminCategoryRequest;
use App\Kullanici;
use App\Models\Kategori;
use App\Models\Siparis;
use App\Repositories\Concrete\BaseRepository;
use App\Repositories\Interfaces\KategoriInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KategoriController extends Controller
{
    protected $model;

    public function __construct(KategoriInterface $model)
    {
        $this->model = $model;
    }

    public function listCategories()
    {
        $query = request('q');
        $main_cat = request('parent_category');
        if ($query || $main_cat) {
            $list = $this->model->getCategoriesByHasCategoryAndFilterText($main_cat, $query, true);
        } else {
            $list = $this->model->with('parent_cat', null, true);
        }
        $main_categories = $this->model->all([['parent_category', null]]);
        return view('admin.category.list_categories', compact('list', 'main_categories'));
    }

    public function newOrEditCategory($category_id = 0)
    {
        $categories = $this->model->all();
        $category = new Kategori();
        if ($category_id != 0) {
            $category = $this->model->getById($category_id);
        }
        return view('admin.category.new_edit_category', compact('category', 'categories'));
    }

    public function saveCategory(AdminCategoryRequest $request, $category_id = 0)
    {
        $request_data = \request()->only('title', 'parent_category', 'icon', 'spot', 'row');
        $request_data['active'] = request()->has('active') ? 1 : 0;
        $request_data['slug'] = str_slug(\request('title'));
        if ($this->model->all([['slug', $request_data['slug']], ['id', '!=', $category_id]])->count() > 0) {
            return back()->withInput()->withErrors('slug alanı zaten kayıtlı');
        }
        if ($category_id != 0) {
            $entry = $this->model->update($request_data, $category_id);
        } else {
            $entry = $this->model->create($request_data);
        }
        Kategori::clearCache();
        return redirect(route('admin.category.edit', $entry->id));
    }

    public function deleteCategory($category_id)
    {
        $this->model->delete($category_id);
        Kategori::clearCache();
        return redirect(route('admin.categories'));
    }
}
