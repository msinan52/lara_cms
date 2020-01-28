<?php

namespace App\Http\Controllers;

use App\Favori;
use App\Repositories\Interfaces\FavorilerInterface;
use App\Repositories\Interfaces\UrunlerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class FavoriController extends Controller
{
    private $_favoriteService;
    private $_productService;

    public function __construct(FavorilerInterface $favoriteService, UrunlerInterface $_productService)
    {
        $this->_favoriteService = $favoriteService;
        $this->_productService = $_productService;
    }

    public function list()
    {
        $userId = auth()->user()->id;
        $list = $this->_favoriteService->all(['user_id' => $userId],null, ['product']);
        return view('site.kullanici.favorites', compact('list'));
    }

    public function listAnonimUserFavorites()
    {
        $list = [];
        $data = json_decode(@$_COOKIE['favoriteProducts'], true);
        if (!is_null($data)) {
            $list = $this->_favoriteService->getAnomimUserFavoritesList($data);
        }
        return view('site.kullanici.anonimFavorites', compact('list'));
    }

    public function addToFavorites()
    {
        $productId = \request()->get('productId');
        $product = $this->_productService->getById($productId);
        if (!is_null($product)) {
            if (auth()->check()) {
                $userId = auth()->user()->id;
                $isAlreadyExists = $this->_favoriteService->all(['user_id' => $userId, 'product_id' => $productId])->count();
                if ($isAlreadyExists == 0) {
                    $this->_favoriteService->create(['user_id' => $userId, 'product_id' => $productId]);
                }
                return "true";
            } else {
                $productIdCookie = $productId;
                $data = json_decode(@$_COOKIE['favoriteProducts'], true);
                if (!is_null($data)) {
                    if (!is_array($data)) {
                        $data = array($data);
                    }
                    if (!in_array($productId, $data))
                        array_push($data, $productIdCookie);
                } else {
                    $data = array($productId);
                }
                setcookie('favoriteProducts', json_encode($data), time() + 3600);
//                $data = json_decode($_COOKIE['favoriteProducts'], true);
                return "true";
            }
        } else {
            return response()->json('ürün bulunamadı');
        }
    }
}
