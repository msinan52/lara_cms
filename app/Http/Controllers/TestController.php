<?php

namespace App\Http\Controllers;


use App\Models\Auth\Permission;
use App\Models\Urun;
use GuzzleHttp\Client;

class TestController extends Controller
{
    public function index()
    {
       $justSuperAdminExcludedControllers = Permission::select('id')->whereNotIn('name',Permission::justSuperAdminAccessThisControllers())->get('id')->pluck('id')->toarray();
       dd($justSuperAdminExcludedControllers);


    }

}
