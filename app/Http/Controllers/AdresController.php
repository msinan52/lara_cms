<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\AccountInterface;
use App\Repositories\Interfaces\CityTownInterface;
use App\Repositories\Interfaces\KullaniciInterface;
use App\Repositories\Interfaces\LogInterface;
use Illuminate\Http\Request;

class AdresController extends Controller
{
    private $_accountService;
    private $_cityTownService;

    public function __construct(AccountInterface $accountService, CityTownInterface $cityTownService)
    {
        $this->_accountService = $accountService;
        $this->_cityTownService = $cityTownService;
    }

    public function address()
    {
        $user = auth()->user();
        $address = $this->_accountService->getUserAddress(auth()->id(), 1);
        $invoiceAddress = $this->_accountService->getUserAddress(auth()->id(), 2);
        $userDefaultAddress = @$this->_accountService->getUserDefaultAddress(auth()->id())->id;
        $userDefaultInvoiceAddress = @$this->_accountService->getUserDefaultInvoiceAddress(auth()->id())->id;
        if (!is_null($userDefaultAddress))
            session()->put('selectedAddressId', $userDefaultAddress);
        return view('site.odeme.odemeAdres', compact('address', 'userDefaultAddress', 'userDefaultInvoiceAddress', 'invoiceAddress'));
    }

    public function setDefaultAddress($id)
    {
        $status = false;
        if (!is_null($id)) {
            $status = $this->_accountService->setUserDefaultAddress(auth()->id(), $id);
        }
        if ($status === true) {
            session()->put('selectedAddressId', intval($id));
            return redirect(route('odemeView'))->with('message', "Adres bilgisi kaydedildi");
        } else
            return redirect(route('odeme.adres'))->with('message', config('constants.messages.error_message'));
    }
}
