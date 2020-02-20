<?php

namespace App\Http\Controllers;

use App\Mail\SiteContactMail;
use App\Models\Ayar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class IletisimController extends Controller
{
    public function index()
    {
        $site = Ayar::getCache();

        return view('site.iletisim.iletisim', compact('site'));
    }

    public function sendMail()
    {
        try {
            $data = \request()->only('name', 'email', 'message', 'phone');
            Mail::to(env('MAIL_USERNAME'))->send(new SiteContactMail($data));
            return back()->with('message', "Mesajınız alındı yakında sizinle iletişime geçeçeğiz");
        } catch (\Exception $exception) {
            session()->flash('message', "Mesajı göndeririken hata oluştu daha sonra tekrar deneyin");
            session()->flash('message_type', "danger");
            return back();
        }
    }
}
