<?php

namespace App\Http\Controllers;

use App\Jobs\SendUserVerificationMail;
use App\Kullanici;
use App\Mail\KullaniciKayitMail;
use App\Models\KullaniciDetay;
use App\Models\Sepet;
use App\Models\SepetUrun;
use App\Models\UrunVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Cart;

class KullaniciController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function loginForm()
    {

        return view('site.kullanici.login');
    }

    public function login()
    {
        $this->validate(request(), ['email' => 'required|email', 'password' => 'required']);
        if (auth()->attempt(['email' => request('email'), 'password' => request('password'), 'is_active' => 1], request()->get('rememberme', 0))) {
            request()->session()->regenerate(); // session bilgisini yenilemek için kullandık
            $current_basket_id = Sepet::getCreate_current_basket_id();
            session()->put('current_basket_id', $current_basket_id);

            if (Cart::count() > 0) {
                foreach (Cart::content() as $cartItem) {
                    if ($cartItem->options->selectedSubAttributesIdList) {
                        $variant = UrunVariant::urunHasVariant($cartItem->id, $cartItem->options->selectedSubAttributesIdList);
                        if ($variant !== false) {
                            $cartItem->price = $variant->price;
                        }
                    }
                    SepetUrun::updateOrCreate(
                        ['sepet_id' => $current_basket_id, 'product_id' => $cartItem->id, 'attributes_text' => $cartItem->options->attributeText],
                        ['qty' => $cartItem->qty, 'price' => $cartItem->price,
                            'status' => SepetUrun::STATUS_ONAY_BEKLIYOR, 'attributes_text' => $cartItem->options->attributeText]
                    );
                }
            }

            Cart::destroy();
            $basket_products = SepetUrun::where('sepet_id', $current_basket_id)->get();
            foreach ($basket_products as $basketItem) {
                Cart::add($basketItem->product->id, $basketItem->product->title, $basketItem->qty, $basketItem->price, ['slug' => $basketItem->product->slug, 'attributeText' => $basketItem->attributes_text, 'image' => $basketItem->product->image]);
            }

            return redirect()->intended('/')->with('message', 'Hoşgeldin Giriş Başarılı');
        } else {
            $errors = ['email' => 'hatalı kullanıcı adı veya şifre kontrol ediniz'];  // dinamik olarak hata mesajı oluşturduk
            return back()->withErrors($errors);
        }
    }

    public function logout()
    {
        auth()->logout();
        request()->session()->flush();
        request()->session()->regenerate();
        return redirect(route('homeView'));
    }

    public function registerForm()
    {
        return view('site.kullanici.register');
    }

    public function register()
    {
        $this->validate(request(), [
            'name' => 'required|min:3|max:60',
            'surname' => 'required|min:3|max:60',
            'email' => 'required|min:5|max:60|email|unique:kullanicilar',
            'password' => 'required|min:5|max:60|confirmed',
        ]);


        $user = Kullanici::create([
            'name' => request('name'),
            'surname' => request('surname'),
            'email' => request('email'),
            'password' => Hash::make(request('password')),
            'activation_code' => Str::random(60),
            'is_active' => 0
        ]);

        $user->detail()->save(new KullaniciDetay());

        $this->dispatch(new SendUserVerificationMail(\request('email'), $user));


        return redirect()->to('/')
            ->with('message', 'kullanıcı kaydınızı aktifleştirmek için lütfen mail adresinizi kontrol ediniz ve aktivasyonu gerçekleştiriniz')
            ->with('message_type', 'warning');
    }

    public function activateUser($activation_code)
    {
        $user = Kullanici::where('activation_code', $activation_code)->first();
        if (!is_null($user)) {
            $user->activation_code = null;
            $user->is_active = true;
            $user->save();
            return redirect()->to('/')
                ->with('message', 'Kullanıcı kaydınız başarıyla tamamlandı')
                ->with('message_type', 'success');
        } else {
            return redirect()->to('/')
                ->with('message', 'Gönderilen doğrulama bilgisi (token) için süre dolmuş veya geçersiz token ')
                ->with('message_type', 'danger');
        }
    }

}
