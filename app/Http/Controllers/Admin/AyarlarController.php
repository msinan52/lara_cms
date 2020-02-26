<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ayar;
use App\Repositories\Concrete\Eloquent\ElAyarlarDal;
use App\Repositories\Interfaces\AyarlarInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AyarlarController extends Controller
{
    protected $model;

    public function __construct(AyarlarInterface $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $list = $this->model->all();
        return view('admin.config.list_configs', compact('list'));
    }

    public function newOrEditForm($id = 0)
    {
        $config = new Ayar();
        if ($id > 0) {
            $config = $this->model->getById($id);
        } else {
            if (count($this->model->all()) >= 1) {
                return redirect(route('admin.configs'))->withErrors("En fazla 1 adet site ayarı ekleyebilirsiniz");
            }
        }
        return view('admin.config.newOrEditConfigForm', compact('config'));
    }

    public function save($id = 0)
    {
        \request()->validate(['desc' => 'required']);
        $data = request()->only('title', 'desc', 'domain', 'keywords', 'facebook', 'instagram', 'twitter', 'instagram', 'youtube', 'footer_text', 'phone', 'mail', 'adres', 'amane', 'cargo_price');
        if ($id > 0) {
            $entry = $this->model->update($data, $id);
        } else {
            if (count($this->model->all()) >= 1) {
                $entry = $this->model->create($data);
            } else {
                return redirect(route('admin.configs'))->withErrors("En fazla 1 adet site ayarı ekleyebilirsiniz");
            }
        }
        if ($entry) {
            $entry = $this->model->getById($entry->id);
            Ayar::setCache($entry);
        }
        return redirect(route('admin.config.edit', $id == 0 ? $entry->id : $id));
    }


    public function delete($id)
    {
        $this->model->delete($id);
        return redirect(route('admin.configs'));
    }
}
