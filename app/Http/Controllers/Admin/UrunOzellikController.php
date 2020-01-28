<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminCategoryRequest;
use App\Models\Kategori;
use App\Models\UrunAttribute;
use App\Models\UrunSubAttribute;
use App\Repositories\Interfaces\UrunOzellikInterface;
use App\Http\Controllers\Controller;

class UrunOzellikController extends Controller
{
    protected $model;

    public function __construct(UrunOzellikInterface $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $query = \request()->get('q', null);
        $list = $this->model->allWithPagination([['title', 'like', "%$query%"]]);
        return view('admin.product.attributes.listAttributes', compact('list'));
    }

    public function detail($id = 0)
    {
        if ($id != 0)
            $item = $this->model->getById($id, null, 'subAttributes');
        else
            $item = new UrunAttribute();
        return view('admin.product.attributes.editOrNewAttribute', compact('item'));
    }

    public function save($id = 0)
    {
        $request_data = \request()->only('title');
        $request_data['active'] = request()->has('active') ? 1 : 0;
        $subArray = [];
        foreach (range(0, 10) as $item) {
            if (request()->has("productSubAttributeTitleHidden$item")) {
                array_push($subArray, array(request()->get("productSubAttributeTitleHidden$item"), request()->get("productSubAttributeTitle$item")));
            }
        }
        if ($id != 0) {
            $entry = $this->model->update($request_data, $id);
        } else {
            $entry = $this->model->create($request_data);
        }
        if (!is_null($entry)) {
            foreach ($subArray as $sa) {
                UrunSubAttribute::updateOrCreate(['id' => $sa[0], 'parent_attribute' => $entry->id], ['title' => $sa[1]]);
            }
        }
        UrunAttribute::clearCache();
        return redirect(route('admin.product.attribute.edit', $entry->id));
    }

    public function deleteSubAttribute($id)
    {
        try {
            $subAttribute = UrunSubAttribute::find($id);
            $subAttribute->delete();
            UrunSubAttribute::clearCache();
            return response()->json('true');
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }

    }

    public function delete($category_id)
    {
        $this->model->delete($category_id);
        UrunAttribute::clearCache();
        return redirect(route('admin.product.attribute.list'));
    }


}
