<?php namespace App\Repositories\Concrete\Eloquent;

use App\Models\Kampanya;
use App\Models\KampanyaKategori;
use App\Models\KampanyaMarka;
use App\Models\KampanyaUrun;
use App\Models\Kategori;
use App\Models\KategoriUrun;
use App\Models\Urun;
use App\Models\UrunAttribute;
use App\Models\UrunDetail;
use App\Models\UrunInfo;
use App\Models\UrunMarka;
use App\Models\UrunSubAttribute;
use App\Models\UrunSubDetail;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\KampanyaInterface;
use App\Repositories\Interfaces\UrunlerInterface;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManagerStatic as Image;

class ElKampanyaDal implements KampanyaInterface
{

    protected $model;
    private $_productService;

    public function __construct(Kampanya $model, UrunlerInterface $productService)
    {
        $this->model = app()->makeWith(ElBaseRepository::class, ['model' => $model]);
        $this->_productService = $productService;
    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        return $this->model->all($filter, $columns, $relations)->get();
    }

    public function allWithPagination($filter = null, $columns = array("*"), $perPageItem = null, $relations = null)
    {
        return $this->model->allWithPagination($filter, $columns, $perPageItem);
    }

    public function getById($id, $columns = array('*'), $relations = null)
    {
        return $this->model->getById($id, $columns, $relations);
    }

    public function getByColumn(string $field, $value, $columns = array('*'), $relations = null)
    {
        return $this->model->getByColumn($field, $value, $columns, $relations);
    }

    public function create(array $data)
    {
        $selectedProductsData = $data['selected_products'];
        $selectedCategoriesData = $data['selected_categories'];
        $selectedCompaniesData = $data['selected_companies'];
        unset($data['selected_products']);
        unset($data['selected_categories']);
        unset($data['selected_companies']);
        $entry = $this->model->create($data); // todo : alt satıra bi bak
        $entry = $this->getById($entry->id);
        $this->_updateSelectedCategoriesAndProductsAndCompany($entry, $selectedCategoriesData, $selectedProductsData, $selectedCompaniesData);
        $this->_updateOrCreateProductDiscountedPrice($entry, $selectedProductsData, $selectedCategoriesData, $selectedCompaniesData);
        return $entry;
    }

    public function update(array $data, $id)
    {
        $selectedProductsData = $data['selected_products'];
        $selectedCategoriesData = $data['selected_categories'];
        $selectedCompaniesData = $data['selected_companies'];
        unset($data['selected_products']);
        unset($data['selected_categories']);
        unset($data['selected_companies']);
        $this->model->update($data, $id);
        $entry = $this->getById($id, null, ['campaignProducts', 'campaignCategories', 'campaignCompanies']);
        // bu gğncelleme isteği yapıldığında silinen ürün ve kategorileri bulup silinenlerin fiyatını günceller
        $this->_updateSelectedCategoriesAndProductsAndCompany($entry, $selectedCategoriesData, $selectedProductsData, $selectedCompaniesData);
        $this->_updateOrCreateProductDiscountedPrice($entry, $selectedProductsData, $selectedCategoriesData, $selectedCompaniesData);
        if ($entry->active == false)
            Kampanya::removeCampaignAllProductDiscounts($entry,0);
        return $entry;
    }

    public function delete($id)
    {
        $camp = $this->getById($id, null, ['campaignProducts', 'campaignCategories', 'campaignCompanies']);
        Kampanya::removeCampaignAllProductDiscounts($camp, 0);
        return $this->model->delete($id);
    }


    public function with($relations, $filter = null, bool $paginate = null, int $perPageItem = null)
    {
        return $this->model->with($relations, $filter, $paginate, $perPageItem);
    }

    // bu güncelleme isteği yapıldığında silinen ürün ve kategorileri bulup silinenlerin fiyatını boş olarak günceller
    private function _updateSelectedCategoriesAndProductsAndCompany($campaign, $selectedCategoriesData, $selectedProductsData, $selectedCompaniesData)
    {
        if ($selectedProductsData) {
            $selectedProducts = [];
            foreach ($selectedProductsData as $spd) {
                array_push($selectedProducts, array('product_id' => $spd, 'campaign_id' => $campaign->id));
            }
            $oldCampaignProducts = $campaign->campaignProducts->pluck('id')->toArray();
            $newCampaignProducts = is_null($selectedProductsData) ? [] : $selectedProductsData;
            $deleteOldDiffPriceProducts = array_diff($oldCampaignProducts, $newCampaignProducts);
            $campaign->campaignProducts()->sync($selectedProducts);
        } else {
            $deleteOldDiffPriceProducts = $campaign->campaignProducts->pluck('id')->toArray();
            $campaign->campaignProducts()->detach();
        }
        if (!is_null($selectedCategoriesData)) {
            $selectedCategories = [];
            foreach ($selectedCategoriesData as $scd) {
                array_push($selectedCategories, array('category_id' => $scd, 'campaign_id' => $campaign->id));
            }
            $oldCampaignCategories = $campaign->campaignCategories->pluck('id')->toArray();
            $newCampaignCategories = is_null($selectedCategoriesData) ? [] : $selectedCategoriesData;
            $deleteOldDiffPriceCategories = array_diff($oldCampaignCategories, $newCampaignCategories);
            $campaign->campaignCategories()->sync($selectedCategories);
        } else {
            $deleteOldDiffPriceCategories = $campaign->campaignCategories->pluck('id')->toArray();
            $campaign->campaignCategories()->detach();
        }
        if (!is_null($selectedCompaniesData)) {
            $selectedCompanies = [];
            foreach ($selectedCompaniesData as $scod) {
                array_push($selectedCompanies, array('company_id' => $scod, 'campaign_id' => $campaign->id));
            }
            $oldCampaignCompanies = $campaign->campaignCompanies->pluck('id')->toArray();
            $newCampaignCompanies = is_null($selectedCompaniesData) ? [] : $selectedCompaniesData;
            $deleteOldDiffPriceCompanies = array_diff($oldCampaignCompanies, $newCampaignCompanies);
            $campaign->campaignCompanies()->sync($selectedCompanies);
        } else {
            $deleteOldDiffPriceCompanies = $campaign->campaignCompanies->pluck('id')->toArray();
            $campaign->campaignCompanies()->detach();
        }
        $this->_deleteCampaignProductPrices($campaign, $deleteOldDiffPriceProducts, $deleteOldDiffPriceCategories, $deleteOldDiffPriceCompanies);
    }

    private function _deleteCampaignProductPrices($campaign, $deletedProductIdList, $deletedCategoryList, $deletedCompanyList)
    {
        $products = Urun::whereHas('categories', function ($query) use ($deletedCategoryList) {
            return $query->whereIn('category_id', is_null($deletedCategoryList) ? [] : $deletedCategoryList);
        })->orWhereIn('id', is_null($deletedProductIdList) ? [] : $deletedProductIdList)->orWhereHas('info', function ($query) use ($campaign, $deletedCompanyList) {
            $query->whereIn('company_id', $deletedCompanyList);
        });
        $products->update(['discount_price' => null]);
    }

    // yeni ürün fiyatı ekler veya varolan fiyatı günceller
    private function _updateOrCreateProductDiscountedPrice($campaign, $productIdList, $categoryIdList, $selectedCompaniesData)
    {
        $products = Urun::whereHas('categories', function ($query) use ($categoryIdList) {
            return $query->whereIn('category_id', is_null($categoryIdList) ? [] : $categoryIdList);
        })->when(count(is_null($productIdList) ? [] : $productIdList) > 0, function ($query) use ($productIdList) {
            $query->whereIn('id', is_null($productIdList) ? [] : $productIdList);
        })
            ->when(!is_null($selectedCompaniesData), function ($query) use ($selectedCompaniesData) {
                $query->WhereHas('info', function ($query) use ($selectedCompaniesData) {
                    $query->whereIn('company_id', is_null($selectedCompaniesData) ? [] : $selectedCompaniesData);
                });
            })->when(!is_null($campaign->min_price), function ($query) use ($campaign) {
                $query->where([['price', '>=', $campaign->min_price]]);
            })->get();
        $products->map(function ($item, $key) use ($campaign) {
            if ($campaign->discount_type === 1) {
                $newProductPrice = $item->price - $campaign->discount_amount;
            } else {
                $newProductPrice = round(($item->price - ($item->price * $campaign->discount_amount) / 100), 2);
            }
            $item->discount_price = $newProductPrice;
            $item->save();
        });
    }

    public function uploadCampaignImage($entry, $image_file)
    {
        if ($image_file->isValid()) {
            $file_name = $entry->id . ' - ' . str_slug($entry->title) . '.jpg';
            $image_resize = Image::make($image_file->getRealPath());
            $image_resize->resize(570, 370);
            $image_resize->save(public_path(config('constants.image_paths.campaign_image_folder_path') . $file_name), 50);
            $entry->update(['image' => $file_name]);
        } else {
            session()->flash('message', $image_file->getErrorMessage());
            session()->flash('message_type', 'danger');
            return back()->withErrors($image_file->getErrorMessage());
        }
    }

    public function getCampaignDetail($slug, $order = null, $selectedSubAttributeList = null, $category = null, $brandIdList = null)
    {
        $filteredProductIdList = $this->_productService->filterProductsFilterBySelectedSubAttributeIdList($selectedSubAttributeList);
        $campaign = Kampanya::with(['campaignProducts', 'campaignCategories.getProducts'])->where(['slug' => $slug, 'active' => 1])->firstOrFail();
        $campaignProductsIdList = KampanyaUrun::select('product_id')->where('campaign_id', $campaign->id)->pluck('product_id');
        $campaignCategoryProductsIdList = KategoriUrun::select('product_id')->whereIn('category_id', $campaign->campaignCategories->pluck('id'))->distinct('product_id')->pluck('product_id');
        $companyProductIdList = UrunInfo::select('product_id')->whereIn('company_id', $campaign->campaignCompanies->pluck('id'))->distinct('product_id')->pluck('product_id');
        $newProductIdList = collect([$campaignProductsIdList, $campaignCategoryProductsIdList, $companyProductIdList])->collapse();
        if (!is_null($filteredProductIdList)) {
            $newProductIdList = array_diff($newProductIdList->toArray(), array_diff($newProductIdList->toArray(), $filteredProductIdList));
        }
        $newProductIdList = array_unique((!is_array($newProductIdList) ? $newProductIdList->toArray() : $newProductIdList));
        $campaignProducts = Urun::with('detail')->whereIn('id', $newProductIdList)->whereHas('categories', function ($query) use ($category) {
            $category = Kategori::whereSlug($category)->first();
            if (!is_null($category)) {
                return $query->where('category_id', $category->id);
            }
        })->when(!is_null($brandIdList), function ($q) use ($brandIdList) {
            $q->whereHas('info', function ($query) use ($brandIdList) {
                $query->whereIn('brand_id', $brandIdList);
            });
        })->select('title', 'price', 'image', 'id', 'slug', 'discount_price')->orderBy(Urun::getProductOrderType($order)[0], Urun::getProductOrderType($order)[1]);
        $newProductIdList = $campaignProducts->pluck('id');
        $productTotalCount = Urun::whereIn('id', $newProductIdList)->select('id')->count();
        $totalPage = ceil($productTotalCount / Urun::getPerPageStatic());
        $subAttributeIdList = UrunSubDetail::select('sub_attribute')->whereHas('parentDetail', function ($query) use ($newProductIdList) {
            $query->whereIn('product', $newProductIdList);
        })->pluck('sub_attribute')->toArray();
        $attributeIdList = UrunAttribute::getActiveAttributesWithSubAttributesCache()->whereIn('id', UrunDetail::whereIn('product', $newProductIdList)->pluck('parent_attribute')->toArray())->pluck('id')->toArray();
        $returnedSubAttributes = UrunSubAttribute::getActiveSubAttributesCache()->whereIn('id', $subAttributeIdList)->whereIn('parent_attribute', $attributeIdList)->pluck('id')->toArray();
        $productDetails = UrunDetail::select('parent_attribute', 'id')->with('subDetails')->whereIn('product', $newProductIdList);
        $attributesIdList = $productDetails->pluck("parent_attribute");
        $attributes = UrunAttribute::getActiveAttributesWithSubAttributesCache()->find($attributesIdList);
        $subAttributesIdList = UrunSubDetail::select('sub_attribute')->whereIn('parent_detail', $productDetails->pluck('id'))->pluck('sub_attribute');
        $subAttributes = UrunSubAttribute::getActiveSubAttributesCache()->find($subAttributesIdList);
        $brandIdList = UrunInfo::select('brand_id')->whereNotNull('brand_id')->whereIn('product_id', $newProductIdList)->distinct('brand_id')->get()->pluck('brand_id')->toArray();
        $brands = array_values(UrunMarka::getActiveBrandsCache()->find($brandIdList)->toArray());
        return [
            'products' => $campaignProducts->simplePaginate(),
            'brands' => $brands,
            'totalPage' => $totalPage != 0 ? $totalPage : 1,
            'productTotalCount' => $productTotalCount,
            'campaign' => $campaign,
            'attributes' => $attributes,
            'subAttributes' => $subAttributes,
            'categories' => $campaign->campaignCategories,
            'returnedSubAttributes' => $returnedSubAttributes,
            'filterSideBarAttr' => $attributeIdList
        ];
    }


    public function getLatestActiveCampaigns($qty)
    {
        $cache = Cache::get("cacheLatestActiveCampaigns{$qty}");
        if (is_null($cache))
            $cache = Cache::remember("cacheLatestActiveCampaigns{$qty}", 24 * 60, function () {
                return Kampanya::select('title', 'slug', 'image')->whereActive(1)->get();
            });
        return $cache;
    }
}
