<?php

namespace App\Http\Controllers;

use App\Models\Urun;
use App\Models\Kategori;
use App\Models\UrunMarka;
use App\Models\UrunYorum;
use App\Repositories\Concrete\BaseRepository;
use App\Repositories\Interfaces\KampanyaInterface;
use App\Repositories\Interfaces\KategoriInterface;
use App\Repositories\Interfaces\UrunlerInterface;
use Illuminate\Http\Request;

class UrunController extends Controller
{
    protected $model;
    private $_categoryService;
    private $_campaignService;

    public function __construct(UrunlerInterface $model, KategoriInterface $categoryService, KampanyaInterface $campaignService)
    {
        $this->model = $model;
        $this->_categoryService = $categoryService;
        $this->_campaignService = $campaignService;
    }

    public function detail($urunSlug)
    {
        $urun = $this->model->getProductDetailWithRelations($urunSlug, ['categories', 'detail.attribute', 'detail.subDetails.parentSubAttribute', 'getLastActive10Comments.user', 'info.brand']);
        if (is_null($urun))
            abort(404);
        $featuredProducts = $this->model->getFeaturedProducts($urun->categories[0]->id, 5, $urun->id, 'detail', ['title', 'price', 'discount_price', 'image', 'slug', 'id']);
//        dd($featuredProducts->toArray());
        $featuredProductTitle = "Benzer Ürünler";
        $bestSellers = collect($this->model->getBestSellersProducts($urun->categories[0]->id, 6, $urun->id));
        $discount = $urun->discount_price;
        $comments = $urun->getLastActive10Comments;
        return view('site.urun.urun', compact('urun', 'discount', 'featuredProducts', 'featuredProductTitle', 'bestSellers', 'commentCount', 'comments'));
    }

    public function addNewComment()
    {
        $productSlug = \request()->get('product_slug');
        try {
            $message = \request()->get('message');
            $user = auth()->user()->id;
            $product = \request()->get('product_id');
            UrunYorum::create(['message' => str_limit($message, 255), 'product_id' => $product, 'user_id' => $user]);
            session()->flash('message', "Yorum eklendi yönetici onayından sonra burada görüntülenecektir");
            return redirect()->route('productDetail', $productSlug);

        } catch (\Exception $e) {
            session()->flash('message', "Yorum eklenirken bir hata oluştu");
            session()->flash('message_type', "danger");
            return redirect()->route('productDetail', $productSlug);
        }
    }

    public function getProductVariantPriceAndQtyWithAjax()
    {
        $productId = request()->get('productId');
        $selectedAttributeIdList = request()->get('selectedAttributeIdList');
        return response()->json($this->model->getProductVariantPriceAndQty($productId, $selectedAttributeIdList));
    }

    public function quickView($slug)
    {
        $product = $this->model->getByColumn('slug', $slug, null, ['variants', 'images']);
        $discount = $product->discount_price;
        return view('site.urun.partials.quickView', compact('product', 'discount'));
    }

    public function getActiveProductBrandsJson()
    {
        $brands = UrunMarka::getActiveBrandsCache();
        return response()->json($brands);
    }

}
