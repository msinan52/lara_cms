<?php namespace App\Repositories\Concrete\Eloquent;

use App\Models\Ayar;
use App\Models\Kategori;
use App\Models\Urun;
use App\Models\UrunAttribute;
use App\Models\UrunDetail;
use App\Models\UrunInfo;
use App\Models\UrunMarka;
use App\Models\UrunSubAttribute;
use App\Models\UrunSubDetail;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\KategoriInterface;
use App\Repositories\Interfaces\UrunlerInterface;
use Illuminate\Support\Facades\DB;

class ElKategoriDal implements KategoriInterface
{

    protected $model;
    private $_productService;

    public function __construct(Kategori $model, UrunlerInterface $productService)
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
        // TODO: Implement allWithPagination() method.
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
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->update($data, $id);
    }

    public function delete($id)
    {
        $category = $this->model->delete($id);
        $category->getProducts()->detach();
        $category->slug = str_random(16);
        $category->save();
        return $category;
    }


    public function with($relations, $filter = null, bool $paginate = null, int $perPageItem = null)
    {
        return $this->model->with($relations, $filter, $paginate, $perPageItem);
    }

    public function getSubCategoriesByCategoryId($categoryId, $count = 10, $orderBy = null)
    {
        return $this->model->all([['parent_category', $categoryId]])->take($count)->get();
    }

    public function orderByProducts($orderType, $productList)
    {
        $perPage = 2;
        if ($orderType == 'yeni') {
            $products = $productList
                ->orderByDesc('updated_at')
                ->paginate($perPage);
        } else if ($orderType == 'artanfiyat') {
            $products = $productList
                ->orderBy('price')
                ->paginate($perPage);
        } else if ($orderType == 'azalanfiyat') {
            $products = $productList
                ->orderByDesc('price')
                ->paginate($perPage);
        } else {
            $products = $productList->paginate($perPage);
        }
        return $products;
    }

    public function getCategoriesByHasCategoryAndFilterText($category_id, $search_text, $paginate = false)
    {
        return Kategori::with('parent_cat')->when(is_numeric($category_id), function ($query) use ($category_id) {
            return $query->where('parent_category', $category_id);
        })->where('title', 'like', "%$search_text%")->simplePaginate();
    }

    public function getProductsAndAttributeSubAttributesByCategory($category, $sub_categories)
    {
        $products = Urun::select('id', 'title', 'image', 'price', 'slug', 'discount_price')->whereHas('categories', function ($query) use ($sub_categories, $category) {
            $sub_categories = $sub_categories->pluck('id')->toArray();
            array_push($sub_categories, $category->id);
            if (count($sub_categories) > 0)
                $query->whereIn('category_id', $sub_categories);
            else
                $query->where('category_id', $category->id);
        })->whereActive(1)->orderByDesc('id');
        $productIdList = $products->pluck("id")->toArray();
        $productTotalCount = Urun::whereIn('id', $productIdList)->select('id')->whereIn('id', $productIdList)->count();
        $totalPage = ceil($productTotalCount / Urun::getPerPageStatic());
        $productDetails = UrunDetail::select('parent_attribute', 'id')->with('subDetails')->whereIn('product', $productIdList);
        $attributesIdList = $productDetails->pluck("parent_attribute");
        $attributes = UrunAttribute::getActiveAttributesWithSubAttributesCache()->find($attributesIdList);

        $subAttributesIdList = UrunSubDetail::select('sub_attribute')->whereIn('parent_detail', $productDetails->pluck('id'))->pluck('sub_attribute');
        $subAttributes = UrunSubAttribute::getActiveSubAttributesCache()->find($subAttributesIdList);
        $brandIdList = UrunInfo::select('brand_id')->whereNotNull('brand_id')->whereIn('product_id', $productIdList)->distinct('brand_id')->get()->pluck('brand_id')->toArray();
        $brands = UrunMarka::getActiveBrandsCache()->find($brandIdList);
        $products = $products->skip(0)->take(Urun::getPerPageStatic())->get();
        return [
            'products' => $products,
            'brands' => $brands,
            'attributes' => $attributes,
            'totalPage' => $totalPage != 0 ? $totalPage : 1,
            'productTotalCount' => $productTotalCount,
            'subAttributes' => $subAttributes,
            'subCategories' => $sub_categories,
            'per_page' => Urun::getPerPageStatic(),
            'current_page' => 1,
        ];
    }

    public function getProductsAttributesSubAttributesProductFilterWithAjax($categorySlug, $orderType, $selectedSubAttributeIdList, $selectedBrandIdList, $currentPage = 1)
    {
        $newProductIdList = $this->_productService->filterProductsFilterBySelectedSubAttributeIdList($selectedSubAttributeIdList);
        $category = Kategori::with('sub_categories')->whereSlug($categorySlug)->first();
        $products = Urun::with('detail')->select('id', 'title', 'slug', 'price', 'image', 'discount_price')->whereHas('categories', function ($query) use ($categorySlug, $category) {
            if (count($category->sub_categories) > 0) {
                $sub_categories = $category->sub_categories->pluck('id')->toArray();
                array_push($sub_categories, $category->id);
                $query->whereIn('category_id', $sub_categories);
            } else
                $query->where('category_id', $category->id);
        })->when(count($selectedSubAttributeIdList) > 0, function ($q) use ($newProductIdList) {
            $q->whereIn('id', $newProductIdList);
        })->when(!is_null($selectedBrandIdList), function ($q) use ($selectedBrandIdList) {
            $q->whereHas('info', function ($query) use ($selectedBrandIdList) {
                $query->whereIn('brand_id', $selectedBrandIdList);
            });
        })->whereActive(1)->orderBy(Urun::getProductOrderType($orderType)[0], Urun::getProductOrderType($orderType)[1]);
        $productIdList = $products->pluck('id')->toArray();
        $productTotalCount = Urun::whereIn('id', $productIdList)->select('id')->count();
        $totalPage = ceil($productTotalCount / Urun::getPerPageStatic());
        $subAttributeIdList = UrunSubDetail::whereHas('parentDetail', function ($query) use ($productIdList) {
            $query->whereIn('product', $productIdList);
        })->pluck('sub_attribute')->toArray();
        $attributeIdList = UrunAttribute::getActiveAttributesWithSubAttributesCache()->whereIn('id', UrunDetail::whereIn('product', $productIdList)->pluck('parent_attribute')->toArray())->pluck('id')->toArray();
        $returnedSubAttributes = UrunSubAttribute::getActiveSubAttributesCache()->whereIn('id', $subAttributeIdList)->whereIn('parent_attribute', $attributeIdList)->pluck('id')->toArray();
        $brandIdList = UrunInfo::select('brand_id')->whereNotNull('brand_id')->whereIn('product_id', $productIdList)->distinct('brand_id')->get()->pluck('brand_id')->toArray();
        $products = $products->skip(($currentPage != 1 ? ($currentPage - 1) : 0) * Urun::getPerPageStatic())->take(Urun::getPerPageStatic())->get();
        $brands = UrunMarka::getActiveBrandsCache()->find($brandIdList)->pluck('id')->toArray();
        return [
            'status' => true,
            'brands' => $brands,
            'totalPage' => $totalPage != 0 ? $totalPage : 1,
            'products' => $products,
            'productTotalCount' => $productTotalCount,
            'returnedSubAttributes' => $returnedSubAttributes,
            'filterSideBarAttr' => $attributeIdList,
            'per_page' => Urun::getPerPageStatic(),
            'current_page' => intval($currentPage != 0 ? $currentPage : 1),
        ];
    }
}
