<?php

namespace App\Http\Controllers\Admin;

use App\Models\UrunFirma;
use App\Repositories\Interfaces\UrunFirmaInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UrunFirmaController extends Controller
{
    protected $model;

    public function __construct(UrunFirmaInterface $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $query = \request()->get('q', null);
        $list = $this->model->allWithPagination([['title', 'like', "%$query%"]]);
        return view('admin.product.company.listProductCompany', compact('list'));
    }

    public function detail($id = 0)
    {
        if ($id != 0)
            $item = $this->model->getById($id, null);
        else
            $item = new UrunFirma();
        return view('admin.product.company.newOrEditProductCompany', compact('item'));
    }

    public function save($id = 0)
    {
        $request_data = \request()->only('title', 'address', 'phone', 'email');
        $request_data['active'] = request()->has('active') ? 1 : 0;
        $request_data['slug'] = str_slug(request('title'));
        if ($this->model->all([['slug', $request_data['slug']], ['id', '!=', $id]], ['id'])->count() > 0) {
            return back()->withInput()->withErrors('slug alanı zaten kayıtlı farklı isim deneyin');
        }
        if ($this->model->all([['email', $request_data['email']], ['id', '!=', $id]], ['id'])->count() > 0) {
            return back()->withInput()->withErrors('email  kayıtlı farklı email deneyin');
        }
        if ($id != 0) {
            $entry = $this->model->update($request_data, $id);
        } else {
            $entry = $this->model->create($request_data);
        }
        return redirect(route('admin.product.company.edit', $entry->id));
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect(route('admin.product.company.list'));
    }
}
