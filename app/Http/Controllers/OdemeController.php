<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentValidationRequest;
use App\Jobs\NewOrderAddedJob;
use App\Models\Ayar;
use App\Models\Iyzico;
use App\Models\IyzicoFails;
use App\Models\İyzicoFailsJson;
use App\Models\KullaniciAdres;
use App\Models\Log;
use App\Models\Sepet;
use App\Models\SepetUrun;
use App\Models\Siparis;
use App\Models\SiteOwnerModel;
use App\Repositories\Interfaces\AccountInterface;
use App\Repositories\Interfaces\CityTownInterface;
use App\Repositories\Interfaces\KuponInterface;
use App\Repositories\Interfaces\OdemeInterface;
use App\Repositories\Interfaces\SepetInterface;
use App\Repositories\Interfaces\SiparisInterface;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OdemeController extends Controller
{
    private $_sepetService;
    private $_siparisService;
    private $_odemeService;
    private $_cityTownService;
    private $_accountService;
    private $_kuponService;

    public function __construct(SepetInterface $sepetService, SiparisInterface $siparisService, OdemeInterface $odemeService, CityTownInterface $cityTownService, AccountInterface $accountService, KuponInterface $kuponService)
    {
        $this->_sepetService = $sepetService;
        $this->_siparisService = $siparisService;
        $this->_odemeService = $odemeService;
        $this->_cityTownService = $cityTownService;
        $this->_accountService = $accountService;
        $this->_kuponService = $kuponService;
    }

    public function index()
    {

        $currentBasket = $this->_sepetService->getById(session()->get('current_basket_id'), null, ['basket_items']);
        if (!auth()->check()) {
            return redirect()->route('kullaniciLoginView')->with('message', 'Ödeme yapmak için oturum açmanız veya üye olmanız gerekmektedir')->with('message_type', 'info');
        } else if (count(Cart::content()) == 0 || is_null($currentBasket)) {
            return redirect()->route('homeView')->with('message', 'Ödeme yapmak için sepetinizde bir ürün bulunamadı')->with('message_type', 'info');
        } else if (count($currentBasket->basket_items) == 0) {
            return redirect()->route('basketView')->withErrors('Ödeme yapmak için sepetinizde bir ürün bulunamadı');
        }
        $address = $this->_accountService->getUserDefaultAddress(auth()->id());
        if (is_null($address)) {
            return redirect()->route('odeme.adres')->with('message', "Herhangi bir adres bilgisi girilmedi/seçilmedi lütfen aşağıdan yeni adres ekleyiniz veya seçiniz")->with('message_type', 'danger');
        }
        $cities = $this->_cityTownService->all();
        $prices = $this->calculateOrderSubTotalCargoCouponAndTotal($currentBasket);
        $defaultInvoiceAddress = $this->_accountService->getUserDefaultInvoiceAddress(auth()->id());
        $owner = SiteOwnerModel::orderByDesc('id')->first();
        if (is_null($owner)) {
            session()->flash('message', 'Site sahibi bilgileri girilmediği için ödeme alınamıyor.Site sahibi ile iletişime geçiniz');
            session()->flash('message_type', 'danger');
            return back();
        }
        return view('site.odeme.odeme', compact('user_detail', 'address', 'cities', 'defaultInvoiceAddress', 'prices', 'owner'));
    }

    private function checkCouponUsableAndReturnCoupon($basket, $cartSubTotalPrice)
    {
        if (!is_null($basket->coupon)) {
            $basketCoupon = $this->_kuponService->getById($basket->coupon);
            if (!is_null($basketCoupon)) {
                $data = $this->_kuponService->checkCoupon(Cart::content(), $basketCoupon->code, $cartSubTotalPrice, $basket);
                if ($data['status'] == false) {
                    session()->flash('message', $data['message']);
                    session()->flash('message_type', 'danger');
                    return null;
                }
                return $basketCoupon;
            }
        }
        return null;
    }

    private function calculateOrderSubTotalCargoCouponAndTotal($currentBasket)
    {
        $cartSubTotalPrice = Cart::subTotal();
        $basketCoupon = $this->checkCouponUsableAndReturnCoupon($currentBasket, $cartSubTotalPrice);
        $cargoPrice = !is_null(Ayar::getCache()) ? Ayar::getCache()->cargo_price : config('constants.cargo_price');
        $cartTotalPrice = $cartSubTotalPrice + $cargoPrice;
        if (!is_null($basketCoupon)) {
            $cartTotalPrice -= $basketCoupon->discount_price;
        }
        return [
            'cartSubTotalPrice' => $cartSubTotalPrice,
            'basketCoupon' => $basketCoupon,
            'cargoPrice' => $cargoPrice,
            'cartTotalPrice' => $cartTotalPrice,
        ];
    }

    public function getIyzicoInstallmentCount()
    {
        $validator = Validator::make(request()->all(), [
            'totalPrice' => 'required|numeric|between:0,3000',
            'creditCartNumber' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $fails = $validator->messages();
            return response()->json(['status' => $fails]);
        }
        $creditCartNumber = substr(\request('creditCartNumber'), 0, 6);
        $totalPrice = \request('totalPrice');
        $data = $this->_odemeService->getIyzicoInstallmentCount($creditCartNumber, $totalPrice);
        return $data->getRawResult();
    }

    public function payment(PaymentValidationRequest $request)
    {
        $current_basket = Sepet::with(['basket_items.product', 'user', 'order'])->find(session()->get('current_basket_id', null));
        if (is_null($current_basket)) {
            return redirect()->route('basketView')->withErrors('Sepetiniz Bulunamadı');
        }
        $newInvoiceAddress = $this->_accountService->getUserDefaultInvoiceAddress(auth()->id());
        $defaultAddress = $this->_accountService->getUserDefaultAddress(auth()->id());
        $this->_odemeService->deleteUserOldNotPaymentOrderTransactions(auth()->user()->id);
        if (\request()->has('differentBillAddress')) {
            $newInvoiceAddress = \request()->only('title', 'name', 'surname', 'phone', 'city', 'town', 'adres');
            $newInvoiceAddress['type'] = KullaniciAdres::TYPE_INVOICE;
            $newInvoiceAddress['user'] = auth()->user()->id;
            $newInvoiceAddress = $this->_accountService->updateOrCreateUserAddress(0, $newInvoiceAddress, auth()->id());
        }
        if (is_null($newInvoiceAddress)) {
            $newInvoiceAddress = $defaultAddress;
        }
        $prices = $this->calculateOrderSubTotalCargoCouponAndTotal($current_basket);
        $order_detail = $this->getOrderDetailFromInvoiceModelAndRequest($newInvoiceAddress, $defaultAddress, $prices);
        $card_info = $this->getCardInfoFromRequest(\request());
        $payment = $this->_odemeService->makeIyzicoPayment($order_detail, $current_basket, $current_basket->basket_items, $card_info, $current_basket->user, $newInvoiceAddress, $defaultAddress, $prices['cartSubTotalPrice'], $prices['cartTotalPrice']);
        if ($payment->status === "success") {
            $iyzico3DResponse = $this->getIyzico3DSecurityDetailsFromIyzicoResponseData($payment);
            Session::put('conversationId', $iyzico3DResponse['conversationId']);
            Session::put('threeDSHtmlContent', $iyzico3DResponse['threeDSHtmlContent']);
            $this->createOrderBeforeThreeDsSecurity($order_detail, $payment, $current_basket, $prices);
            return redirect()->route('odeme.threeDSecurityRequest');
        } else {
            $this->_odemeService->logPaymentError($payment, $order_detail);
            return back()->withInput()
                ->with('message', $payment->errorMessage)->with('message_type', 'danger');
        }
    }

    // showed threeDSecurityHTml
    public function threeDSecurityRequest()
    {
        $orderId = \session()->get('orderId');
        if (is_null($orderId))
            return redirect()->route('odemeView')->withErrors('ödeme yapmak için herhangi bir siparişiniz bulunamadı');
        return view('site.odeme.iyzico.threeDSecurity');
    }

    // iyzico 3ds response posted this view
    public function threeDSecurityResponse()
    {
        $requestData = \request()->only('status', 'paymentId', 'conversationId', 'mdStatus');
        $orderId = \session()->get('orderId');
        $order = $this->_siparisService->getById($orderId);
        if ($requestData['status'] == 'success') {
            $isThreeDSCompleted = $this->_odemeService->completeIyzico3DSecurityPayment($requestData['conversationId'], $requestData['paymentId']);
            if ($isThreeDSCompleted !== false) {
                if (strtolower($isThreeDSCompleted['status']) === "success") {
                    $this->completeOrderStatusChangeToTrue($requestData['conversationId']);
                    $iyzicoModelData = $this->getIyzicoDetailsFromIyzicoResponseData($isThreeDSCompleted);
                    $this->_siparisService->createOrderIyzicoDetail($iyzicoModelData, \session()->get('orderId'), json_encode($isThreeDSCompleted, JSON_UNESCAPED_UNICODE));
                    return redirect()->route('siparisView')->with('message', 'sipariş başarılı şekilde alındı');
                } else {
                    $message = (array)$isThreeDSCompleted['errorMessage'];
                    İyzicoFailsJson::addLog(null, $order->full_name, $order->sepet_id, json_encode($isThreeDSCompleted, JSON_UNESCAPED_UNICODE));
                    $this->_siparisService->delete($orderId);
                    return redirect()->route('odemeView')->withErrors($message);
                }
            } else {
                $this->_siparisService->delete($orderId);
                return redirect()->route('odemeView')->withErrors("işlem sırasında bir hata oluştu site sahibi ile iletişime geçiniz");
            }
        } else {
            $this->_siparisService->delete($orderId);
            return redirect()->route('odemeView')->withErrors(Iyzico::getMdStatusByParam($requestData['mdStatus']));
        }
    }

    public function createOrderBeforeThreeDsSecurity($order_detail, $payment, $current_basket, $prices)
    {
        $order = $this->_siparisService->create($order_detail);
        \session()->put('orderId', $order->id);
        return true;
    }

    public function completeOrderStatusChangeToTrue($orderId)
    {
        $basket = $this->_sepetService->getById($orderId, null, ['order', 'user', 'basket_items']);
        $orderId = \session()->get('orderId', null);
        $order = $this->_siparisService->getById($orderId);
        if (!is_null($basket) && $order !== null) {
            $basketCoupon = $this->_kuponService->getById($basket->coupon);
            $prices = [
                'cartSubTotalPrice' => $order->order_price,
                'basketCoupon' => $basketCoupon,
                'cargoPrice' => $order->cargo_price,
                'cartTotalPrice' => $order->order_total_price,
            ];
            $basket->order->is_payment = 1;
            $basket->order->save();
            $productIdListAndSelectedSubAttributesList = array();
            $this->dispatch(new NewOrderAddedJob($basket->user->email, $basket->user, $order, $basket->basket_items, $prices));
            foreach (Cart::content() as $cartItem) {
                array_push($productIdListAndSelectedSubAttributesList, array($cartItem->id, $cartItem->options->selectedSubAttributesIdList, $cartItem->qty));
            }
            $this->_siparisService->decrementProductQty($productIdListAndSelectedSubAttributesList);
            $this->_kuponService->decrementCouponQty($basket->coupon);
            Cart::destroy();
            $basket_item_id_list = $basket->basket_items->pluck('id');
            SepetUrun::whereIn('id', $basket_item_id_list)->update(['status' => SepetUrun::STATUS_SIPARIS_ALINDI]);
            session()->forget('current_basket_id');
            return true;
        }
        return false;
    }

    private function getCardInfoFromRequest($request)
    {
        $cardInfo = array(
            "holderName" => $request->get('holderName', null),
            "cardNumber" => $request->get('cardnumber', null),
            "cardExpireDateMonth" => $request->get('cardexpiredatemonth', null),
            "cardExpireDateYear" => $request->get('cardexpiredateyear', null),
            "cvv" => $request->get('cardcvv2', null),
        );
        return $cardInfo;
    }

    private function getOrderDetailFromInvoiceModelAndRequest($invoiceAddress, $defaultAddress, $prices)
    {
        $order = request()->only('taksit_sayisi', 'cardnumber', 'holderName', 'cardexpiredatemonth', 'cardexpiredateyear', 'cardcvv2', 'installment_count');
        $order['sepet_id'] = session()->get('current_basket_id');
        $order['phone'] = $defaultAddress->phone;
        $order['installment_count'] = \request()->get('taksit_sayisi', 1);
        $order['status'] = Siparis::STATUS_SIPARIS_ALINDI;
        $order['order_price'] = $prices['cartSubTotalPrice'];
        $order['cargo_price'] = $prices['cargoPrice'];
        $order['order_total_price'] = $prices['cartTotalPrice'];
        $order['ip_adres'] = \request()->ip();
        $order['adres'] = $defaultAddress->adres . ' ' . $defaultAddress->Town->title . '/' . $defaultAddress->City->title;
        $order['fatura_adres'] = $invoiceAddress->adres . ' ' . $invoiceAddress->Town->title . '/' . $invoiceAddress->City->title . '--' . $invoiceAddress->name . ' ' . $invoiceAddress->surname;
        $order['full_name'] = $defaultAddress->name . ' ' . $defaultAddress->surname;
        return $order;
    }

    private function getIyzicoDetailsFromIyzicoResponseData($paymentArray)
    {
        $paymentData['status'] = $paymentArray['status'];
        $paymentData['transaction_id'] = $paymentArray['conversationId'];
        $paymentData['price'] = $paymentArray['price'];
        $paymentData['paidPrice'] = $paymentArray['paidPrice'];
        $paymentData['installment'] = $paymentArray['installment'];
        $paymentData['paymentId'] = $paymentArray['paymentId'];
        $paymentData['basketId'] = $paymentArray['basketId'];
        return $paymentData;

    }

    private function getIyzico3DSecurityDetailsFromIyzicoResponseData($paymentJson)
    {
        $paymentData['status'] = $paymentJson->status;
        $paymentData['conversationId'] = $paymentJson->conversationId;
        $paymentData['threeDSHtmlContent'] = $paymentJson->threeDSHtmlContent;
        return $paymentData;

    }
}
