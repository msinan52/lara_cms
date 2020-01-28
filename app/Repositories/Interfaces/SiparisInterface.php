<?php namespace App\Repositories\Interfaces;

interface SiparisInterface extends BaseRepositoryInterface
{
    public function createOrderIyzicoDetail($iyzicoData, $orderId, $iyzicoJsonResponse);

    public function orderFilterByStatusAndSearchText($search_text, $status, $paginate = false);

    public function getUserAllOrders(int $user_id);

    public function getOrderIyzicoDetail($id);

    public function getUserOrderDetailById(int $user_id, int $order_id);

    public function updateOrderWithItemsStatus($order_id, $order_data, $order_items_status);

    // $productIdListAndSelectedSubAttributesList = [productID,array(subAttributeIdList),qty]
    public function decrementProductQty($productIdListQtyAndSelectedSubAttributesList, $qty = 1);

    public function getIyzicoErrorLogs($query);
}
