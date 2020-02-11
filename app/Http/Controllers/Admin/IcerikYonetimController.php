<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ContentManagementRequest;
use App\Models\Content;
use App\Models\SiteOwnerModel;
use App\Repositories\Interfaces\IcerikYonetimInterface;
use App\Http\Controllers\Controller;

class IcerikYonetimController extends Controller
{
    protected $model;

    public function __construct(IcerikYonetimInterface $model)
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
        return view('admin.content.listContents', compact('list'));
    }

    public function newOrEditForm($id = 0)
    {
        $item = new Content();
        if ($id != 0) {
            $item = $this->model->getById($id);
        }
        $languages = SiteOwnerModel::activeLanguages();
//        dd($languages);
        return view('admin.content.newOrEditContent', compact('item', 'languages'));
    }

    public function save(ContentManagementRequest $request, $id = 0)
    {
        $request_data = \request()->only('title', 'spot', 'desc', 'lang');
        $request_data['active'] = request()->has('active') ? 1 : 0;
        $i = 0;
        $request_data['slug'] = str_slug(request('title'));
        while ($this->model->all([['slug', $request_data['slug']], ['id', '!=', $id]], ['id'])->count() > 0) {
            $request_data['slug'] = str_slug(request('title')) . '-' . $i;
            $i++;
        }
        if ($id != 0) {
            $entry = $this->model->update($request_data, $id);
        } else {
            $entry = $this->model->create($request_data);
        }
        if (request()->hasFile('image') && $entry) {
            $this->validate(request(), [
                'image' => 'image|mimes:jpg,png,jpeg,gif|max:2048'
            ]);
            $this->model->uploadMainImage($entry, request()->file('image'));
        }
        if ($entry)
            return redirect(route('admin.content.edit', $entry->id));
        return back();
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect(route('admin.content'));
    }
}
