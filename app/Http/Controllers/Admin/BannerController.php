<?php

namespace App\Http\Controllers\Admin;
use App\Models\Banner;
use App\Repositories\Interfaces\BannerInterface;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    protected $model;

    public function __construct(BannerInterface $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $query = request('q');
        if ($query) {
            $list = $this->model->allWithPagination([['title', 'like', "%$query%"]]);
        } else {
            $list = $this->model->allWithPagination();
        }
        return view('admin.banner.listBanners', compact('list'));
    }

    public function newOrEditForm($id = 0)
    {
        $banner = new Banner();
        if ($id != 0) {
            $banner = $this->model->getById($id);
        }
        return view('admin.banner.newOrEditBanner', compact('banner'));
    }

    public function save($id = 0)
    {
        $request_data = \request()->only('title', 'sub_title', 'image', 'link','lang');
        $request_data['active'] = request()->has('active') ? 1 : 0;
        if ($id != 0) {
            $entry = $this->model->update($request_data, $id);
        } else {
            $entry = $this->model->create($request_data);
        }
        if (request()->hasFile('image')) {
            $this->validate(request(), [
                'image' => 'image|mimes:jpg,png,jpeg,gif|max:2048'
            ]);
            $this->model->uploadBannerImage($entry, request()->file('image'));
        }
        if (!is_null($entry))
            return redirect(route('admin.banners'));
        else
            return redirect(route('admin.banners.edit', $id));
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect(route('admin.banners'));
    }
}
