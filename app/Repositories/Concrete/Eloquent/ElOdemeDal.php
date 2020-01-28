<?php namespace App\Repositories\Concrete\Eloquent;

use App\Models\IyzicoFails;
use App\Models\İyzicoFailsJson;
use App\Models\Log;
use App\Models\Siparis;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\OdemeInterface;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Request;

class ElOdemeDal implements OdemeInterface
{

//    protected $model;
//
//    public function __construct(Log $model)
//    {
//        $this->model = app()->makeWith(ElBaseRepository::class, ['model' => $model]);
//    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        // TODO: Implement all() method.
    }

    public function allWithPagination($filter = null, $columns = array("*"), $perPageItem = null, $relations = null)
    {
//        return $this->model->allWithPagination($filter, $columns, $perPageItem);
    }


    public function getById($id, $columns = array('*'), $relations = null)
    {
    }

    public function getByColumn(string $field, $value, $columns = array('*'), $relations = null)
    {
        // TODO: Implement getByColumn() method.
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function with($relations, $filter = null, bool $paginate = null, int $perPageItem = null)
    {
        // TODO: Implement with() method.
    }

    public function getIyzicoInstallmentCount($creditCartNumber, $totalPrice)
    {
        # create request class
        $options = $this->getIyzicoOptions();
        $request = new \Iyzipay\Request\RetrieveInstallmentInfoRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123123");
        $request->setBinNumber($creditCartNumber);
        $request->setPrice($totalPrice);
        $installmentInfo = \Iyzipay\Model\InstallmentInfo::retrieve($request, $options);
        return $installmentInfo;
    }

    public function getIyzicoOptions()
    {
        $options = new \Iyzipay\Options();
        $options->setApiKey(env('IYZIPAY_API_KEY'));
        $options->setSecretKey(env('IYZIPAY_SECRET_KEY'));
        $options->setBaseUrl("https://sandbox-api.iyzipay.com");
        return $options;
    }

    public function makeIyzicoPayment($order, $basket, $basketItemsOnDB, $cardInfo, $user, $invoiceAddress, $address, $subTotalPrice, $totalPrice)
    {
        $options = $this->getIyzicoOptions();
        $request = new \Iyzipay\Request\CreatePaymentRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($basket->id);
        $request->setPrice($subTotalPrice);
        $request->setPaidPrice($totalPrice);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setInstallment($order['taksit_sayisi']);
        $request->setBasketId($basket->id);
        $request->setPaymentChannel(\Iyzipay\Model\PaymentChannel::WEB);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);

        $paymentCard = new \Iyzipay\Model\PaymentCard();
        $paymentCard->setCardHolderName($cardInfo['holderName']);
        $paymentCard->setCardNumber(str_replace("-", "", $cardInfo['cardNumber']));
        $paymentCard->setExpireMonth($cardInfo['cardExpireDateMonth']);
        $paymentCard->setExpireYear($cardInfo['cardExpireDateYear']);
        $paymentCard->setCvc($cardInfo['cvv']);
        $paymentCard->setRegisterCard(0);
        $request->setPaymentCard($paymentCard);

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId('' . $user->id);
        $buyer->setName($address->name);
        $buyer->setSurname($address->surname);
        $buyer->setGsmNumber('+90' . str_replace(" ", "", str_replace(")", "", str_replace("(", "", str_replace("-", "", $order['phone'])))));
        $buyer->setEmail($user->email);
        $buyer->setIdentityNumber("74300864791");
        $buyer->setRegistrationAddress($order['adres']);
        $buyer->setIp(request()->ip());
        $buyer->setCity($address->City->title);
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($address->name . ' ' . $address->surname);
        $shippingAddress->setCity($address->City->title);
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress($order['adres']);
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($invoiceAddress->name . ' ' . $invoiceAddress->surname);
        $billingAddress->setCity($invoiceAddress->City->title);
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress($order['fatura_adres']);
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);

        $basketItems = array();
        foreach ($basketItemsOnDB as $item) {
            $basketItem = new \Iyzipay\Model\BasketItem();
            $basketItem->setId($item->id);
            $basketItem->setName($item->product->title);
            $basketItem->setCategory1($item->product->categories->pluck('title')->first());
            $basketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
            $basketItem->setPrice($item->price * $item->qty);
            array_push($basketItems, $basketItem);
        }
        $request->setBasketItems($basketItems);
        $request->setCallbackUrl(route('odeme.threeDSecurityResponse'));
        $response = \Iyzipay\Model\ThreedsInitialize::create($request, $options);
        return json_decode($response->getRawResult());
    }

    public function logPaymentError($paymentResult, $order)
    {
        try {
            $paymentResult = json_decode($paymentResult, JSON_UNESCAPED_UNICODE);
            İyzicoFailsJson::addLog(auth()->user()->id, $order['full_name'], $order['sepet_id'], $paymentResult);
        } catch (\Exception $exception) {
            Log::addLog('iyzicoFailure', $exception->getMessage(), $exception);
        }

    }

    public function completeIyzico3DSecurityPayment($conversationId, $paymentId)
    {
        try {
            $request = new \Iyzipay\Request\CreateThreedsPaymentRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId($conversationId);
            $request->setPaymentId($paymentId);
            $respo = \Iyzipay\Model\ThreedsPayment::create($request, $this->getIyzicoOptions());
            return json_decode($respo->getRawResult(), true);
        } catch (\Exception $exception) {
            Log::addLog('iyzicoFailure', ("conversation Id :" . $conversationId . ' - Payment ID : ' . $paymentId), $exception);
            return false;
        }

    }

    public function deleteUserOldNotPaymentOrderTransactions($userId)
    {
        Siparis::where('is_payment', 0)->whereHas('basket', function ($query) use ($userId) {
            $query->user_id = $userId;
        })->forceDelete();
    }
}
