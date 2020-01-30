<?php


namespace App\Repositories\Traits;


trait ResponseTrait
{
    public function response($status, $message, $redirectTo = '', $data = null)
    {
        return [
            'status' => $status,
            'message' => $message,
            'alert' => $status == true ? 'success' : 'danger',
            'data' => $data,
            'redirectTo' => $redirectTo
        ];
    }
}
