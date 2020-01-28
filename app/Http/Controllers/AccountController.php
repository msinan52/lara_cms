<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDetailSaveRequest;
use App\Kullanici;
use App\Models\KullaniciAdres;
use App\Models\Log;
use App\Repositories\Interfaces\AccountInterface;
use App\Repositories\Interfaces\CityTownInterface;
use App\Repositories\Interfaces\KullaniciInterface;
use App\Repositories\Interfaces\LogInterface;
use App\Rules\PhoneNumberRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    private $_accountService;
    private $_cityTownService;
    private $_userService;
    private $_logService;

    public function __construct(AccountInterface $accountService, CityTownInterface $cityTownService, KullaniciInterface $userService, LogInterface $logService)
    {
        $this->_accountService = $accountService;
        $this->_userService = $userService;
        $this->_cityTownService = $cityTownService;
        $this->_logService = $logService;
    }

    public function dashboard()
    {
        $userId = auth()->user()->id;
        $user = Kullanici::with(['detail.address', 'detail.invoiceAddress'])->find($userId);
        return view('site.kullanici.dashboard', compact('user'));
    }

    public function addressView()
    {
        $type = \request()->get('type', KullaniciAdres::TYPE_DELIVERY);
        $address = $this->_accountService->getUserAddress(auth()->id(), $type);
        $userDefaultAddress = @$this->_accountService->getUserDefaultAddress(auth()->id())->id;
        $userDefaultInvoiceAddress = @$this->_accountService->getUserDefaultInvoiceAddress(auth()->id())->id;
        return view('site.kullanici.adres', compact('address', 'userDefaultAddress', 'userDefaultInvoiceAddress'));
    }

    public function setDefaultAddress($id)
    {
        $status = false;
        if (!is_null($id)) {
            $status = $this->_accountService->setUserDefaultAddress(auth()->id(), $id);
        }
        if ($status === true)
            return redirect(route('kullanici.address'))->with('message', config('constants.messages.success_message'));
        else
            return redirect(route('kullanici.address'))->with('message', config('constants.messages.error_message'));
    }

    public function setDefaultInvoiceAddress($id, $redirectRouteName = null)
    {
        $redirectRouteName = is_null($redirectRouteName) ? route('kullanici.address') : route($redirectRouteName);
        $redirectRouteName .= '?type2';
        $status = false;
        if (!is_null($id)) {
            $status = $this->_accountService->setUserDefaultInvoiceAddress(auth()->id(), $id);
        }
        if ($status === true)
            return redirect($redirectRouteName)->with('message', config('constants.messages.success_message'));
        else
            return redirect($redirectRouteName)->with('message', config('constants.messages.error_message'));
    }

    public function addressDetail($id, $redirectRouteName = null)
    {
        if (is_null($redirectRouteName))
            $redirectUrl = route('kullanici.address');
        else
            $redirectUrl = route($redirectRouteName);
        $id = intval($id);
        $cities = $this->_cityTownService->all(['active' => 1], null, ['towns']);
        $towns = [];
        if ($id !== 0) {
            $address = $this->_accountService->getAddressById($id);
            $towns = $this->_cityTownService->getTownsByCityId($address->city);
        } else
            $address = new KullaniciAdres();
        return view('site.kullanici.adresDetail', compact('address', 'cities', 'towns', 'redirectUrl'));
    }

    public function addressSave()
    {
        $status = true;
        $errors = [];
        $data = null;
        $validatedData = \Validator::make(request()->get('data'), [
            'title' => 'required|max:50', 'name' => 'required|max:50', 'surname' => 'required|max:50',
            'phone' => ['required', 'max:20', new PhoneNumberRule(\request()->get('data')['phone'])],
            'city' => 'required|numeric', 'town' => 'required|numeric',
            'adres' => 'required|max:255',
        ]);
        if ($validatedData->errors()->count() > 0) {
            $status = false;
            $errors = json_decode($validatedData->errors());
        } else {
            $reqData = collect(\request()->get('data'));
            $adresId = $reqData->get('adresId');
            $filtered = $reqData->except('adresId');
            $filtered->put('user', auth()->id());
            $data = $this->_accountService->updateOrCreateUserAddress($adresId, $filtered->all(), auth()->user()->id);
        }
        $data = ['status' => $status, 'errors' => $errors, 'data' => $data, 'redirect' => route('kullanici.address')];
        return response()->json($data);
    }

    public function userDetail()
    {
        $user = auth()->user();
        return view('site.kullanici.userDetail', compact('user'));
    }

    public function userDetailSave(UserDetailSaveRequest $request)
    {
        $data = \request()->only('name', 'surname');
        if (\request()->filled('changePasswordCheckbox'))
            $data['password'] = Hash::make(request('password'));
        $this->_userService->update($data, auth()->user()->id);
        return redirect(route('kullanici.user.detail'));
    }


    public function userLogErrors()
    {
        $user = auth()->user();
        $logs = $this->_logService->getLogsByUserId($user->id);
        return view('site.kullanici.userErrorLogs', compact('user', 'logs'));
    }
}
