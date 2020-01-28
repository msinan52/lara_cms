<?php namespace App\Repositories\Interfaces;

interface OdemeInterface extends BaseRepositoryInterface
{
    public function getIyzicoInstallmentCount($creditCartNumber, $totalPrice);

    public function getIyzicoOptions();

    public function makeIyzicoPayment($order, $basket, $basketItemsOnDB, $cardInfo, $user, $invoiceAddress, $address, $subTotalPrice, $totalPrice);

    public function logPaymentError($paymentResult, $order);

    public function completeIyzico3DSecurityPayment($conversationId, $paymentId);

    public function deleteUserOldNotPaymentOrderTransactions($userId);
}
