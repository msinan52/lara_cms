<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "log";
    protected $guarded = [];
    protected $perPage = 20;

    public static function addLog(string $level, string $message, string $exception, string $code = null, string $url = null, int $user_id = null)
    {
        Log::create([
            'user_id' => $user_id == null ? Auth::user() ? Auth::user()->id : 0 : $user_id,
            'level' => substr($level, 0, 15),
            'message' => substr($message, 0, 250),
            'exception' => (string)$exception,
            'code' => $code == null ? str_random(16) : $code,
            'url' => $url == null ? substr(request()->fullUrl(), 0, 150) : substr($url, 0, 150)
        ]);
    }
}
