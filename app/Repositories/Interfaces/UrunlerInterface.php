<?php namespace App\Repositories\Interfaces;

use App\Models\Urun;

interface UrunlerInterface extends BaseRepositoryInterface
{
    public function getProductDetailWithRelations($slug, $relations);

    public function getProductsByHasCategoryAndFilterText($category_id, $search_text, $company_id);

    public function updateWithCategory(array $data, int $id, array $categories, array $selected_attributes_and_sub_attributes);

    public function createWithCategory(array $data, array $categories, array $selected_attributes_and_sub_attributes);

    public function uploadProductMainImage($product, $image_file);

    public function getAllAttributes();

    public function getAllSubAttributes();

    public function getSubAttributesByAttributeId(int $id);

    public function deleteProductDetail($detailId);

    public function getProductDetailWithSubAttributes($productId);

    public function deleteProductVariant($variant_id);

    public function saveProductVariants($product_id, $selected_variant_attribute_id_list, $variant_price, $variant_qty, $variantId);

    public function getProductVariantPriceAndQty($product_id, $sub_attribute_id_list);

    public function deleteProductImage($id);

    public function addProductImageGallery($product_id, $image_files, $entry);

    public function getProductsAndAttributeSubAttributesByFilter($category, $searchKey, $currentPage = 1, $selectedSubAttributeList = null, $selectedBrandIdList = null, $orderType = null);

    public function getProductsBySearchTextForAjax($searchQuery);

    public function getFeaturedProducts($categoryId = null, $qty = 10, $excludeProductId = null, $relations = null, $columns = array("*"));

    public function getBestSellersProducts($categoryId = null, $qty = 9, $excludeProductId = null);

    public function filterProductsFilterBySelectedSubAttributeIdList($selectedSubAttributeList);


}
