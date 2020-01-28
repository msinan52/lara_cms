<?php

namespace App\Http\Controllers;

use App\Jobs\SendUserVerificationMail;
use App\Kullanici;
use App\Models\Banner;
use App\Models\Kampanya;
use App\Models\Kategori;
use App\Models\Urun;
use App\Models\UrunAttribute;
use App\Models\UrunSubAttribute;
use App\Repositories\Interfaces\KampanyaInterface;
use App\Repositories\Interfaces\SSSInterface;
use App\Repositories\Interfaces\UrunlerInterface;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnasayfaController extends Controller
{
    private $_productService;
    private $_campService;
    private $_sssService;

    public function __construct(UrunlerInterface $productService, KampanyaInterface $campService, SSSInterface $sssService)
    {
        $this->_productService = $productService;
        $this->_campService = $campService;
        $this->_sssService = $sssService;
    }

    public function index()
    {
        $categories = Kategori::getCache();
        $bestSellers = $this->_productService->getBestSellersProducts(null, 10);
        $featuredProducts = $this->_productService->getFeaturedProducts(null, 9);
        $bestSellersTitles = ['Yeni', 'En Favoriler', 'En Çok Sepetlenenler'];
        $banners = Banner::whereActive(true)->take(6)->orderByDesc('id')->get();
        $camps = $this->_campService->getLatestActiveCampaigns(3);
        return view("site.index", compact('categories', 'bestSellers', 'banners', 'featuredProductTitle', 'featuredProducts', 'bestSellersTitles', 'camps'));
    }

    public function sss()
    {
        $sss = $this->_sssService->all(['active' => 1]);
        return view('site.layouts.sss', compact('sss'));
    }
}
