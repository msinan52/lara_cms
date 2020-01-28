<?php namespace App\Repositories\Interfaces;

interface KuponInterface extends BaseRepositoryInterface
{
    public function checkCoupon($cartItems, $couponCode, $cartSubTotalPrice, $basket);

    public function decrementCouponQty($couponId);
}
