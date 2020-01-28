<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderAdminNotificationMail;
use App\Mail\NewUserOrderAddedMail;
use App\Models\Ayar;
use App\Models\Iyzico;
use App\Models\Siparis;
use App\Models\UrunInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function index()
    {
        $users = DB::select("select * from urunler_info where JSON_CONTAINS(oems,'1','$[0].marka' )");
        dd($users);
    }

}
