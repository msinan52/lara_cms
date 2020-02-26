<?php

namespace App\Http\Controllers\Admin;

use App\Models\OurTeam;
use App\Repositories\Interfaces\OurTeamInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OurTeamController extends Controller
{
    protected $model;

    public function __construct(OurTeamInterface $model)
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
        return view('admin.ourTeam.listOurTeam', compact('list'));
    }

    public function newOrEditForm($id = 0)
    {
        $item = new OurTeam();
        if ($id != 0) {
            $item = $this->model->getById($id);
        }
        return view('admin.ourTeam.newOrEditOurTeam', compact('item'));
    }

    public function save($id = 0)
    {
        $request_data = \request()->only('title', 'position', 'image', 'desc');
        $request_data['active'] = request()->has('active') ? 1 : 0;
        if ($id != 0) {
            $entry = $this->model->update($request_data, $id);
        } else {
            $entry = $this->model->create($request_data);
        }
        if (request()->hasFile('image') && $entry) {
            $this->validate(request(), [
                'image' => 'image|mimes:jpg,png,jpeg,gif|max:2048'
            ]);
            $this->model->uploadImage($entry, request()->file('image'));
        }
        if (!is_null($entry))
            return redirect(route('admin.our_team.edit', $id));
        return back()->withInput();
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect(route('admin.our_team'));
    }
}
