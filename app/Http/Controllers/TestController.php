<?php

namespace App\Http\Controllers;


use App\Models\Auth\Permission;
use Illuminate\Support\Str;
use function Sodium\compare;

class TestController extends Controller
{
    public function index()
    {
        $array = [100, 200, 300];

        $p = sha1('murat');
        dd($p);
        $justSuperAdminExcludedControllers = Permission::select('id')->whereNotIn('name', Permission::justSuperAdminAccessThisControllers())->get('id')->pluck('id')->toarray();
        dd($justSuperAdminExcludedControllers);
    }

}
