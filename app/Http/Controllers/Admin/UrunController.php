<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminProductSaveRequest;
use App\Models\Urun;
use App\Models\UrunDetail;
use App\Models\UrunDetay;
use App\Models\UrunSubAttribute;
use App\Models\UrunVariant;
use App\Models\UrunVariantSubAttribute;
use App\Models\Vehicles\AracBeygirGucu;
use App\Models\Vehicles\AracKasa;
use App\Models\Vehicles\AracMarka;
use App\Models\Vehicles\AracModel;
use App\Models\Vehicles\AracModelYili;
use App\Models\Vehicles\AracMotorHacmi;
use App\Repositories\Interfaces\AyarlarInterface;
use App\Repositories\Interfaces\KategoriInterface;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\UrunFirmaInterface;
use App\Repositories\Interfaces\UrunlerInterface;
use App\Repositories\Interfaces\UrunMarkaInterface;
use function GuzzleHttp\Psr7\str;

class UrunController extends Controller
{
    protected $model;
    protected $categoryService;
    private $_brandService;
    private $_productCompanyService;

    public function __construct(UrunlerInterface $model, KategoriInterface $categoryService, UrunMarkaInterface $brandService, UrunFirmaInterface $productCompanyService)
    {
        $this->model = $model;
        $this->_brandService = $brandService;
        $this->categoryService = $categoryService;
        $this->_productCompanyService = $productCompanyService;
        $this->middleware('admin')->except('getProductVariantPriceAndQtyWithAjax');
    }

    public function getAllProductsForSearchAjax()
    {
        $query = request()->get('text');
        $data = $this->model->getProductsBySearchTextForAjax($query);
        return response()->json($data);
    }

    public function listProducts()
    {
        $search = request('q', "");
        $selected_category = request('category_filter');
        $selected_company = request('company_filter');
        $companies = $this->_productCompanyService->all();
        if ($search || $selected_category || $selected_company) {
            $list = $this->model->getProductsByHasCategoryAndFilterText($selected_category, $search, $selected_company);
        } else {
            $list = $this->model->with(['categories'], null, true);
        }
        $categories = $this->categoryService->all();
        return view('admin.product.list_products', compact('list', 'categories', 'companies'));
    }


    public function newOrEditProduct($product_id = 0)
    {
        $categories = $this->categoryService->all();
        $product = new Urun();
        $selected_categories = $productDetails = $productVariants = $productSelectedSubAttributesIdsPerAttribute = $selectedAttributeIdList = $productSelectedSubAttributesIdsPerAttribute = [];
        $brands = $this->_brandService->all(['active' => 1]);
        $companies = $this->_productCompanyService->all(['active' => 1]);
        if ($product_id != 0) {
            $product = $this->model->getById($product_id, null, ['categories', 'variants.urunVariantSubAttributes', 'info']);
            $selected_categories = $product->categories()->pluck('category_id')->all();
        }
        $attributes = $this->model->getAllAttributes();
        $subAttributes = $this->model->getAllSubAttributes();
        if ($product_id != 0) {
            $productDetails = $this->model->getProductDetailWithSubAttributes($product_id)['detail'];
            $productVariants = $product->variants;
            $productSelectedSubAttributesIdsPerAttribute = array();
            foreach ($productDetails as $index => $detail) {
                $selectedAttributeIdList = array();
                foreach ($detail['sub_details'] as $subIndex => $subDetail) {
                    array_push($selectedAttributeIdList, $subDetail['sub_attribute']);
                }
                $productSelectedSubAttributesIdsPerAttribute[$index] = $selectedAttributeIdList;
            }
        }
        return view('admin.product.new_edit_product',
            compact('product', 'categories', 'brands', 'selected_categories', 'attributes', 'productDetails', 'subAttributes', 'productSelectedSubAttributesIdsPerAttribute', 'productVariants', 'companies'));
    }

    public function saveProduct(AdminProductSaveRequest $request, $product_id = 0)
    {
        $posted_categories = request('categories');
        $request_data = request()->only('title', 'slug', 'price', 'desc', 'qty', 'discount_price', 'brand', 'company', 'buying_price', 'spot', 'properties', 'code');
        if (!isset($request_data['properties']))
            $request_data['properties'] = [];
        $request_data['active'] = request()->has('active') ? 1 : 0;
        $i = 0;
        $request_data['slug'] = str_slug(request('title'));
        while ($this->model->all([['slug', $request_data['slug']], ['id', '!=', $product_id]], ['id'])->count() > 0) {
            $request_data['slug'] = str_slug(request('title')) . '-' . $i;
            $i++;
        }
        $productSelectedAttributesIdAnSubAttributeIdList = array();
        $index = 0;
        do {
            if (request()->has("attribute$index")) {
                array_push($productSelectedAttributesIdAnSubAttributeIdList, array(request("attribute$index"), request("subAttributes$index")));
            }
            $index++;
        } while ($index < 10);
        if ($product_id != 0) {
            $entry = $this->model->updateWithCategory($request_data, $product_id, $posted_categories, $productSelectedAttributesIdAnSubAttributeIdList);
        } else {
            $entry = $this->model->createWithCategory($request_data, $posted_categories, $productSelectedAttributesIdAnSubAttributeIdList);
        }
        if ($entry) {
            $productDetailTotalCount = $entry->detail()->count();
            $variantIndex = 0;
            do {
                if (request()->has("variantIndexHidden$variantIndex")) {
                    $variantId = (int)request()->get("variantIndexHidden$variantIndex");
                    $variantQty = (int)request()->get("variantQty$variantIndex");
                    $variantPrice = request()->get("variantPrice$variantIndex");
                    $selectedVariantAttributeIdList = [];
                    for ($a = 0; $a < $productDetailTotalCount; $a++) {
                        if (request()->has("variantAttributeHidden$variantIndex-$a")) {
                            if (is_numeric(request()->get("variantAttributeSelect$variantIndex-$a")))
                                array_push($selectedVariantAttributeIdList, request()->get("variantAttributeSelect$variantIndex-$a"));
                        }
                    }
                    if (count($selectedVariantAttributeIdList) == 0)
                        break;
                    $this->model->saveProductVariants($product_id, $selectedVariantAttributeIdList, $variantPrice, $variantQty, $variantId);
                }
                $variantIndex++;
            } while ($variantIndex < 10);
            if (request()->hasFile('image')) {
                $this->validate(request(), [
                    'image' => 'image|mimes:jpg,png,jpeg,gif|max:'.config('admin.max_upload_size')
                ]);
                $this->model->uploadProductMainImage($entry, request()->file('image'));
            }
            if (request()->hasFile('imageGallery')) {
                $this->validate(request(), [
                    'imageGallery.*' => 'image|mimes:jpg,png,jpeg,gif|max:2048'
                ]);
                $this->model->addProductImageGallery($entry->id, request()->file('imageGallery'), $entry);
            }
            return redirect(route('admin.product.edit', $entry->id));
        }
        return back();
    }

    public function deleteProduct($product_id)
    {
        $this->model->delete($product_id);
        return redirect(route('admin.products'));
    }

    public function getSubAttributesByAttributeId($id)
    {
        return response()->json($this->model->getSubAttributesByAttributeId($id));
    }

    public function getAllProductAttributes()
    {
        return response()->json($this->model->getAllAttributes());
    }

    public function deleteProductDetailById($id)
    {
        return $this->model->deleteProductDetail($id);
    }

    public function getProductDetailWithSubAttributes($product_id)
    {
        return response()->json($this->model->getProductDetailWithSubAttributes($product_id));
    }

    public function deleteProductVariant($variant_id)
    {
        return $this->model->deleteProductVariant($variant_id);
    }

    public function deleteProductImage($id)
    {
        return $this->model->deleteProductImage($id);
    }
}
