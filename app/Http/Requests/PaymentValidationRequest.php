<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardExpirationMonth;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardNumber;

class PaymentValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $cardNumber = str_replace("-", "", request()->get('cardnumber'));
        $phoneReplaces = ['(', ')', '-', ' '];
        request()->merge(['cardnumber' => $cardNumber]);
        request()->merge(['phone' => intval(str_replace($phoneReplaces, "", request('phone')))]);
        $rules = [
            'cardnumber' => ['required', 'min:16'],
            'holderName' => ['required', 'min:5', 'max:50'],
            'cardexpiredateyear' => ['required', new CardExpirationYear($this->get('cardexpiredatemonth'))],
            'cardexpiredatemonth' => ['required', new CardExpirationMonth($this->get('cardexpiredateyear'))],
            'cardcvv2' => ['required', new CardCvc($cardNumber)],

        ];
        if (!is_null(request()->get('differentBillAddress'))) {
            $rules['title'] = ['required', 'min:2', 'max:50'];
            $rules['name'] = ['required', 'min:3', 'max:50'];
            $rules['surname'] = ['required', 'min:3', 'max:50'];
            $rules['phone'] = ['required', new PhoneNumberRule(request('phone'))];
            $rules['city'] = ['required', 'numeric'];
            $rules['town'] = ['required', 'numeric'];
            $rules['adres'] = ['required', 'min:10', 'max:250'];
        }
        return $rules;
    }
}
