<?php

namespace App\Http\Controllers;


use App\Models\Urun;
use GuzzleHttp\Client;

class TestController extends Controller
{
    public function index()
    {
        return response()->streamDownload(function () {
            echo Urun::all();
        }, 'laravel-readme.md');


    }

}
