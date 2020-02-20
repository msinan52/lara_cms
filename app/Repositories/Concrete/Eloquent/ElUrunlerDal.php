<?php namespace App\Repositories\Concrete\Eloquent;

use App\Models\Kategori;
use App\Models\KategoriUrun;
use App\Models\Log;
use App\Models\Urun;
use App\Models\UrunAttribute;
use App\Models\UrunDetail;
use App\Models\UrunImage;
use App\Models\UrunInfo;
use App\Models\UrunMarka;
use App\Models\UrunSubAttribute;
use App\Models\UrunSubDetail;
use App\Models\UrunVariant;
use App\Models\UrunVariantSubAttribute;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\UrunlerInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;
use PHPUnit\Framework\Constraint\Attribute;

class ElUrunlerDal implements UrunlerInterface
{

    protected $model;
    protected $urunAttributeService;
    protected $urunSubAttributeService;
    protected $categoryService;

    public function __construct(Urun $model, UrunAttribute $urunAttributeService, UrunSubAttribute $urunSubAttributeService, Kategori $categoryService)
    {
        $this->model = app()->makeWith(ElBaseRepository::class, ['model' => $model]);
        $this->urunAttributeService = app()->makeWith(ElBaseRepository::class, ['model' => $urunAttributeService]);
        $this->urunSubAttributeService = app()->makeWith(ElBaseRepository::class, ['model' => $urunSubAttributeService]);
        $this->categoryService = app()->makeWith(ElBaseRepository::class, ['model' => $categoryService]);
    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        return $this->model->all($filter, $columns, $relations)->get($columns);
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
        if (is_null($columns))
            $columns = array("*");
        return Urun::select($columns)->where($field, $value)->when($relations != null, function ($query) use ($relations) {
            return $query->with($relations);
        })->withCount('activeComments')->firstOrFail();
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
        $record = $this->getById($id, null, ['images', 'categories', 'favorites', 'commentsForDelete']);
        $record->detail()->delete();
        $record->categories()->detach();
        $record->favorites()->detach();
        $record->commentsForDelete()->delete();
        $deleteImages = [];
        $image_path_minify = getcwd() . config('constants.image_paths.product270x250_folder_path') . $record->image;
        $image_path = getcwd() . config('constants.image_paths.product_image_folder_path') . $record->image;
        array_push($deleteImages, $image_path_minify, $image_path);
        foreach ($record->images as $img) {
            array_push($deleteImages, (getcwd() . config('constants.image_paths.product_gallery_folder_path') . $img->image));
        }
        foreach ($deleteImages as $di) {
            if (file_exists($di))
                \File::delete($di);
        }
        $record->delete();
        return $record;
    }

    public function with($relations, $filter = null, bool $paginate = null, int $perPageItem = null)
    {
        return $this->model->with($relations, $filter, $paginate, $perPageItem);
    }

    public function getProductsByHasCategoryAndFilterText($category_id, $search_text, $company_id)
    {
        return $this->model->with(['categories'], [['title', 'like', "%$search_text%"]])->when(is_numeric($category_id), function ($query) use ($category_id) {
            $query->whereHas('categories', function ($q) use ($category_id) {
                $category = $this->categoryService->getById($category_id);
                $sub_categories = $category->sub_categories->pluck('id')->toArray();
                array_push($sub_categories, $category->id);
                $q->whereIn('category_id', $sub_categories);
            });
        })->when(!is_null($company_id), function ($query) use ($company_id) {
            $query->whereHas('info', function ($query) use ($company_id) {
                if (!is_null($company_id))
                    $query->where('company_id', $company_id);
            });
        })->whereActive(1)->orderByDesc('id')->paginate();
    }

    public function updateWithCategory(array $data, int $id, array $categories, array $selected_attributes_and_sub_attributes = null)
    {
        try {
            $entry = $this->getById($id, null, 'info');
            $infoData = ['brand_id' => $data['brand'], 'product_id' => $id, 'company_id' => $data['company'], 'buying_price' => $data['buying_price'], 'spot' => $data['spot'], 'properties' => $data['properties'], 'code' => $data['code']];
            if (@is_null($entry->info->code) && config('admin.product_auto_code'))
                $infoData['code'] = intval(($entry->id . rand(1000, 999999)));
            $entry->info->updateOrCreate(['product_id' => $id], $infoData);
            unset($data['brand'], $data['company'], $data['buying_price'], $data['spot'], $data['properties'], $data['code']);
            $this->update($data, $id);
            $entry->categories()->sync($categories);
            if ($selected_attributes_and_sub_attributes != null) {
                foreach ($selected_attributes_and_sub_attributes as $attribute) {
                    $productDetail = UrunDetail::firstOrCreate(['product' => $entry->id, 'parent_attribute' => $attribute[0]]);
                    $productDetail->subDetailsForSync()->sync($attribute[1]);
                }
            }
            session()->flash('message', config('constants.messages.success_message'));
            return $entry;
        } catch (\Exception $exception) {

            $message = config('constants.messages.error_message') . '' . $exception->getMessage();
            session()->flash('message', $message);
            return false;
        }
    }

    public function createWithCategory(array $data, array $categories, array $selected_attributes_and_sub_attributes)
    {
        try {
            $buyingPrice = $data['buying_price'];
            $companyId = $data['company'];
            $brandId = $data['brand'];
            $spotText = $data['spot'];
            unset($data['brand'], $data['company'], $data['buying_price'], $data['spot']);
            $entry = $this->create($data);
            $code = config('admin.product_auto_code') ? intval(($entry->id . rand(1000, 999999))) : null;
            $entry->info->create(['brand_id' => $brandId, 'product_id' => $entry->id, 'company_id' => $companyId, 'buying_price' => $buyingPrice, 'spot' => $spotText, 'code' => $code]);
            $entry->categories()->attach($categories);
            if ($selected_attributes_and_sub_attributes != null) {
                foreach ($selected_attributes_and_sub_attributes as $attribute) {
                    $productDetail = UrunDetail::create(['product' => $entry->id, 'parent_attribute' => $attribute[0]]);
                    $productDetail->subDetailsForSync()->attach($attribute[1]);
                }
            }
            session()->flash('message', config('constants.messages.success_message'));
            return $entry;
        } catch (\Exception $exception) {
            $message = config('constants.messages.error_message') . '' . $exception->getMessage();
            session()->flash('message', $message);
            return false;
        }
    }

    public function uploadProductMainImage($product, $image_file)
    {
        if ($image_file->isValid()) {
            $file_name = $product->id . '-' . str_slug($product->title) . '.jpg';
            $image_resize = Image::make($image_file->getRealPath());
            $image_resize->backup();
            $image_resize->resize((getimagesize($image_file)[0] / 2), getimagesize($image_file)[1] / 2);
            $image_resize->save(public_path(config('constants.image_paths.product_image_folder_path') . $file_name), Urun::IMAGE_QUALITY);
            $image_resize->reset();
            if (Urun::IMAGE_RESIZE)
                $image_resize->resize(Urun::IMAGE_RESIZE[0], Urun::IMAGE_RESIZE[1]);
            $image_resize->save(public_path(config('constants.image_paths.product270x250_folder_path') . $file_name), Urun::IMAGE_QUALITY);
            $product->update(['image' => $file_name]);
        } else {
            session()->flash('message', $image_file->getErrorMessage());
            session()->flash('message_type', 'danger');
            return back()->withErrors($image_file->getErrorMessage());
        }
    }

    public function getAllAttributes()
    {
        return $this->urunAttributeService->all(['active' => 1])->get();
    }

    public function getAllSubAttributes()
    {
        return UrunSubAttribute::getActiveSubAttributesCache();
    }

    public function getSubAttributesByAttributeId(int $id)
    {
        return $this->urunSubAttributeService->all([['parent_attribute', $id]])->get();
    }

    public function deleteProductDetail($detailId)
    {
        try {
            $productDetail = UrunDetail::with('product.variants')->findOrFail($detailId);
            //
            $productId = $productDetail->product;
            $subAttributeIdList = $productDetail->attribute->subAttributes->pluck('id');
            $productVariantsIdList = Urun::with('variants')->whereId($productId)->first()->variants()->pluck('id');
            $data = UrunVariantSubAttribute::where(function ($query) use ($productVariantsIdList) {
                foreach ($productVariantsIdList as $variant_id) {
                    $query->orWhere("variant_id", $variant_id);
                }
            })->where(function ($query) use ($subAttributeIdList) {
                foreach ($subAttributeIdList as $subAttributeId) {
                    $query->orWhere("sub_attr_id", $subAttributeId);
                }
            })->delete();
            $productDetail->subDetailsForSync()->detach();
            $productDetail->delete();
            return "true";
        } catch (\Exception $exception) {
            return $exception;
        }

    }

    public function getProductDetailWithSubAttributes($productId)
    {
        $product = $this->getById($productId, ['id', 'title'], 'detail.subDetails.parentSubAttribute');
        foreach ($product->detail as $de) {
            $de['parent_title'] = $de->attribute->title;
        }
        return $product->toArray();
    }

    public function deleteProductVariant($variantId)
    {
        try {
            $productVariant = UrunVariant::findOrFail($variantId);
            $productVariant->urunVariantSubAttributesForSync()->detach();
            $productVariant->delete();
            return "true";
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function saveProductVariants($product_id, $selected_variant_attribute_id_list, $variant_price, $variant_qty, $variantId)
    {
        $canAddNewUrunVariant = true;
        foreach (Urun::find($product_id)->variants as $variant) {
            $canAddNewUrunVariant = $variant->urunVariantSubAttributes->pluck('sub_attr_id')->toArray() == $selected_variant_attribute_id_list ? false : true;
            if ($canAddNewUrunVariant == false)
                break;
        }
        if ($canAddNewUrunVariant == true && $variantId == 0) {
            $variant = UrunVariant::create(['price' => $variant_price, 'qty' => $variant_qty, 'product_id' => $product_id]);
            $variant->urunVariantSubAttributesForSync()->sync($selected_variant_attribute_id_list);
            $variant->save();
        }
        if ($variantId != 0) {
            $variant = UrunVariant::find($variantId);
            $variant->price = $variant_price;
            $variant->urunVariantSubAttributesForSync()->sync($selected_variant_attribute_id_list);
            $variant->qty = $variant_qty;
            $variant->save();
        }
    }

    public function getProductVariantPriceAndQty($product_id, $sub_attribute_id_list)
    {
        $variant = UrunVariant::urunHasVariant($product_id, $sub_attribute_id_list);
        return $variant;
    }

    public function deleteProductImage($id)
    {
        $productImage = UrunImage::find($id);
        if (is_null($productImage))
            return $id . " id li resim bulunamadı";
        $image_path = getcwd() . config('constants.image_paths.product_gallery_folder_path') . $productImage->image;
        $image_path_minify = getcwd() . config('constants.image_paths.product270x250_folder_path') . $productImage->image;
        if (file_exists($image_path)) {
            \File::delete($image_path);

            $productImage->delete();
            return "true";
        }
        return $image_path;

    }

    public function addProductImageGallery($product_id, $image_files, $entry)
    {
        foreach (request()->file("imageGallery") as $index => $file) {
            if ($index < 10) {
                if ($file->isValid()) {
                    $file_name = $product_id . '-' . str_slug($entry->title) . str_random(6) . '.jpg';
                    $image_resize = Image::make($file->getRealPath());
                    $image_resize->resize((getimagesize($file)[0] / 2), getimagesize($file)[1] / 2);
                    $image_resize->save(public_path(config('constants.image_paths.product_gallery_folder_path') . $file_name), 50);
                    UrunImage::create(['product' => $product_id, 'image' => $file_name]);
                }
            } else {
                session()->flash('message', 'ürüne ait en fazla 10 adet resim yükleyebilirsiniz');
                session()->flash('message_type', 'danger');
                break;
            }

        }
        return true;
    }

    public function getProductsAndAttributeSubAttributesByFilter($category, $searchKey, $currentPage = 1, $selectedSubAttributeList = null, $selectedBrandIdList = null, $orderType = null)
    {
        $filteredProductIdList = $this->filterProductsFilterBySelectedSubAttributeIdList($selectedSubAttributeList);
        $products = Urun::with('detail')->where([['title', "like", "%$searchKey%"], ['active', "=", 1]])->when(!is_null($category), function ($query) use ($category) {
            $query->whereHas('categories', function ($query) use ($category) {
                $category = Kategori::with('sub_categories')->find($category);
                if (!is_null($category)) {
                    $sub_categories = $category->sub_categories->pluck('id')->toArray();
                    array_push($sub_categories, $category->id);
                    $query->whereIn('category_id', $sub_categories);
                }
            });
        })->when(!is_null($selectedBrandIdList), function ($q) use ($selectedBrandIdList) {
            $q->whereHas('info', function ($query) use ($selectedBrandIdList) {
                $query->whereIn('brand_id', $selectedBrandIdList);
            });
        })->when(!is_null($filteredProductIdList), function ($q) use ($filteredProductIdList) {
            $q->whereIn('id', $filteredProductIdList);
        })->orderBy(Urun::getProductOrderType($orderType)[0], Urun::getProductOrderType($orderType)[1]);
        $productIdList = $products->pluck('id')->toArray();
        $productTotalCount = Urun::whereIn('id', $productIdList)->select('id')->whereIn('id', $productIdList)->count();
        $totalPage = ceil($productTotalCount / Urun::getPerPageStatic());
        $productDetails = UrunDetail::select('parent_attribute', 'id')->with('subDetails')->whereIn('product', $productIdList);
        $attributesIdList = $productDetails->pluck("parent_attribute");
        $attributes = UrunAttribute::getActiveAttributesWithSubAttributesCache()->find($attributesIdList);
        $categories = KategoriUrun::select('category_id')->whereIn('product_id', $productIdList)->distinct('category_id')->pluck('category_id')->toArray();
        $categories = Kategori::getCache()->whereIn('id', $categories);
        $subAttributesIdList = UrunSubDetail::select('sub_attribute')->whereIn('parent_detail', $productDetails->pluck('id'))->pluck('sub_attribute');
        $subAttributes = UrunSubAttribute::getActiveSubAttributesCache()->find($subAttributesIdList);
        $brandIdList = UrunInfo::select('brand_id')->whereNotNull('brand_id')->whereIn('product_id', $productIdList)->distinct('brand_id')->get()->pluck('brand_id')->toArray();
        $brands = array_values(UrunMarka::getActiveBrandsCache()->find($brandIdList)->toArray());
        $products = $products->skip(($currentPage != 1 ? ($currentPage - 1) : 0) * Urun::getPerPageStatic())->take(Urun::getPerPageStatic())->get();
        return [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'attributes' => $attributes,
            'totalPage' => $totalPage != 0 ? $totalPage : 1,
            'productTotalCount' => $productTotalCount,
            'subAttributes' => $subAttributes,
            'returnedSubAttributes' => $subAttributes->pluck('id')->toArray(),
            'filterSideBarAttr' => $attributes->pluck('id')->toArray(),
            'per_page' => Urun::getPerPageStatic(),
            'current_page' => intval($currentPage != 0 ? $currentPage : 1),
        ];
    }

    public function getProductsBySearchTextForAjax($searchQuery)
    {

        $products = \Cache::remember('allActiveProductsWithKeyTitlePriceId', 60 * 24, function () {
            return Urun::where('active', 1)->get(['id', 'title', 'price']);
        });

        $result = $products->filter(function ($value, $key) use ($searchQuery) {
            return str_contains($value->title, $searchQuery) === true;
        });
        return $result;
    }

    public function getFeaturedProducts($categoryId = null, $qty = 10, $excludeProductId = null, $relations = null, $columns = array("*"))
    {
        if ($categoryId !== null) {
            return Urun::select($columns)->when($relations != null, function ($query) use ($relations) {
                return $query->with($relations);
            })->when($excludeProductId != null, function ($query) use ($excludeProductId) {
                return $query->where('id', '!=', $excludeProductId);
            })->whereActive(1)->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })->
            orderByDesc('id')->take($qty)->get();
        }
        return Urun::select($columns)->when($relations != null, function ($query) use ($relations) {
            return $query->with($relations);
        })->when($excludeProductId != null, function ($query) use ($excludeProductId) {
            return $query->where(['id', '!=', $excludeProductId]);
        })->whereActive(1)->orderByDesc('id')->take($qty)->get();

    }

    public function getBestSellersProducts($categoryId = null, $qty = 9, $excludeProductId = null)
    {
        return \Cache::remember("bestSellersProducts{$categoryId}-{$qty}", 60 * 24, function () use ($excludeProductId, $qty, $categoryId) {
            $excludeProductQuery = $categoryQuery = "";
            if ($excludeProductId !== null) {
                $excludeProductQuery = " and u.id != ${excludeProductId}";
            }
            if ($categoryId !== null) {
                $categoryQuery = " and ku.category_id = {$categoryId}";
            }
            $query = "select u.title,SUM(su.qty) as qty,u.image,u.slug,u.price,u.id,u.discount_price,ud.product as detail
            from siparisler as  si
            inner join  sepet as s on si.sepet_id = s.id
            inner join  sepet_urun as su on s.id = su.sepet_id
            inner join urunler as u on u.active=1 and  su.product_id = u.id {$excludeProductQuery}
            inner join kategori_urun as ku on ku.product_id = u.id {$categoryQuery}
            left join urun_detail ud on u.id = ud.product
            group by u.title,u.image,u.slug,u.price,u.id,ud.product
            order by SUM(su.qty) DESC limit {$qty}";

            return collect(DB::select($query));
        });

    }

    public function filterProductsFilterBySelectedSubAttributeIdList($selectedSubAttributeList)
    {
        if (is_array($selectedSubAttributeList) && @count($selectedSubAttributeList) > 0) {
            $productIdList = array();
            $newProductIdList = array();
            foreach ($selectedSubAttributeList as $index => $item) {
                array_push($productIdList, UrunDetail::whereHas('subDetails', function ($query) use ($item) {
                    $query->whereIn('sub_attribute', $item);
                })->pluck('product')->toArray());
                if ($index !== 0 && $index !== count($selectedSubAttributeList) && count($selectedSubAttributeList) > 1) {
                    $newProductIdList = array_intersect($productIdList[$index - 1], $productIdList[$index]);
                    $productIdList[$index] = $newProductIdList;
                } else
                    $newProductIdList = $productIdList[0];
            }
            return $newProductIdList;
        }
        return null;
    }

    public function getProductDetailWithRelations($slug, $relations)
    {
        return Urun::with($relations)->whereSlug($slug)->whereActive(1)->withCount('comments')->first();
    }
}
