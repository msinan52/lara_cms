<?php

namespace App\Http\Controllers;

use App\Jobs\SendUserVerificationMail;
use App\Kullanici;
use App\Models\Banner;
use App\Models\Kampanya;
use App\Models\Kategori;
use App\Models\SiteOwnerModel;
use App\Models\Urun;
use App\Models\UrunAttribute;
use App\Models\UrunSubAttribute;
use App\Repositories\Interfaces\BannerInterface;
use App\Repositories\Interfaces\KampanyaInterface;
use App\Repositories\Interfaces\SSSInterface;
use App\Repositories\Interfaces\UrunlerInterface;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class AnasayfaController extends Controller
{
    private $_productService;
    private $_campService;

    public function __construct(UrunlerInterface $productService, KampanyaInterface $campService)
    {
        $this->_productService = $productService;
        $this->_campService = $campService;
    }

    public function index()
    {
        $categories = Kategori::getCache();
        dd(SiteOwnerModel::activeLanguages());
//        dump(app()->getLocale());
        $bestSellers = $this->_productService->getBestSellersProducts(null, 10);
        $featuredProducts = $this->_productService->getFeaturedProducts(null, 9);
        $bestSellersTitles = ['Yeni', 'En Favoriler', 'En Çok Sepetlenenler'];
        $banners = Banner::whereActive(true)->take(6)->orderByDesc('id')->get();
        $camps = $this->_campService->getLatestActiveCampaigns(3);
        return view("site.index", compact('categories', 'bestSellers', 'banners', 'featuredProductTitle', 'featuredProducts', 'bestSellersTitles', 'camps'));
    }

    public function sitemap()
    {
        $products = Urun::orderBy('id', 'DESC')->take(1000)->get();
        $categories = Kategori::orderBy('id', 'DESC')->take(1000)->get();
        $now = Carbon::now()->toAtomString();
        $content = view('site.sitemap', compact('products', 'now', 'categories'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    public function setLanguage($locale)
    {
        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->route('homeView');
    }
}
