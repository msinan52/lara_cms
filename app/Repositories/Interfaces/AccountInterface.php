<?php namespace App\Repositories\Interfaces;

interface AccountInterface extends BaseRepositoryInterface
{
    public function getUserAddress($userId, $addressType);

    public function getUserDefaultAddress($userId);

    public function setUserDefaultAddress($userId, $addressId);

    public function getUserDefaultInvoiceAddress($userId);

    public function setUserDefaultInvoiceAddress($userId, $addressId);

    public function getAddressById($addressId);

    public function updateOrCreateUserAddress($id, $data, $userId);
}
