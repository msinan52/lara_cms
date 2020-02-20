<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Repositories\Interfaces\BlogInterface;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    protected $model;

    public function __construct(BlogInterface $model)
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
        return view('admin.blog.listBlogs', compact('list'));
    }

    public function newOrEditForm($id = 0)
    {
        $item = new Blog();
        if ($id != 0) {
            $item = $this->model->getById($id);
        }
        return view('admin.blog.newOrEditBlog', compact('item'));
    }

    public function save($id = 0)
    {
        $request_data = \request()->only('title', 'desc', 'lang');
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
        if (!is_null($entry)) {
            if (request()->hasFile('image')) {
                $this->validate(request(), [
                    'image' => 'image|mimes:jpg,png,jpeg,gif|max:'.config('admin.max_upload_size')
                ]);
                $this->model->uploadImage($entry, request()->file('image'));
            }
            return redirect(route('admin.blog.edit', $entry->id));
        } else
            return back()->withInput();
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect(route('admin.blog'));
    }
}
