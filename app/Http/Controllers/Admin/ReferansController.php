<?php

namespace App\Http\Controllers\Admin;

use App\Models\Referance;
use App\Repositories\Interfaces\ReferenceInterface;
use App\Http\Controllers\Controller;
use function GuzzleHttp\Psr7\str;

class ReferansController extends Controller
{
    private $_referenceService;

    public function __construct(ReferenceInterface $referenceService)
    {
        $this->_referenceService = $referenceService;
    }

    public function list()
    {
        $query = request('q');
        if ($query) {
            $list = $this->_referenceService->allWithPagination([['title', 'like', "%$query%"]]);
        } else {
            $list = $this->_referenceService->allWithPagination();
        }
        return view('admin.references.listReferences', compact('list'));
    }

    public function newOrEditForm($id = 0)
    {
        $item = new Referance();
        if ($id != 0) {
            $item = $this->_referenceService->getById($id);
        }
        return view('admin.references.newOrEditReference', compact('item'));
    }

    public function save($id = 0)
    {
        $request_data = \request()->only('title', 'desc', 'link');
        $request_data['slug'] = str_slug(request('title'));
        if ($this->_referenceService->all([['slug', $request_data['slug']], ['id', '!=', $id]], ['id'])->count() > 0) {
            return back()->withInput()->withErrors('slug alanı zaten kayıtlı farklı başlık deneyin');
        }
        $request_data['active'] = request()->has('active') ? 1 : 0;
        if ($id != 0) {
            $entry = $this->_referenceService->update($request_data, $id);
        } else {
            $entry = $this->_referenceService->create($request_data);
        }
        if (request()->hasFile('image') && $entry) {
            $this->validate(request(), [
                'image' => 'image|mimes:jpg,png,jpeg,gif|max:2048'
            ]);
            $this->_referenceService->uploadMainImage($entry, request()->file('image'));
        }
        if ($entry)
            return redirect(route('admin.reference.edit', $entry->id));
        return back();
    }

    public function delete($id)
    {
        $this->_referenceService->delete($id);
        return redirect(route('admin.reference'));
    }
}
