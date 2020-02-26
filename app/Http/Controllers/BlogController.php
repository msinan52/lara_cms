<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\BlogInterface;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private $_blogService;

    public function __construct(BlogInterface $blogService)
    {
        $this->_blogService = $blogService;
    }

    public function list()
    {
        $list = $this->_blogService->allWithPagination(['active' => 1]);
        return view('site.blog.listBlog', compact('list'));
    }

    public function detail($slug)
    {
        $item = $this->_blogService->getByColumn('slug', $slug);
        if (is_null($item))
            abort(404);
        return view('site.blog.blogDetail', compact('item'));

    }
}
