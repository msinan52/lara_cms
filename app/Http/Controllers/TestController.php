<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderAdminNotificationMail;
use App\Mail\NewUserOrderAddedMail;
use App\Models\Auth\Permission;
use App\Models\Auth\PermissionRole;
use App\Models\Auth\Role;
use App\Models\Ayar;
use App\Models\Iyzico;
use App\Models\Siparis;
use App\Models\UrunInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class TestController extends Controller
{
    public function index()
    {
//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//        PermissionRole::truncate();
//        Permission::truncate();
//        Role::truncate();
//        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
//        DB::table('roles')->insert([
//            ['name' => 'admin'],
//            ['name' => 'operator'],
//            ['name' => 'customer'],
//        ]);
//        $i = 0;
//        foreach (Route::getRoutes()->getRoutes() as $key => $route) {
//            if ($i < 4) {
//                dump($route);
//            }
//        }
    }

}
