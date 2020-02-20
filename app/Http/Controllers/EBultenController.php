<?php

namespace App\Http\Controllers;

use App\Http\Requests\EbultenCreateRequest;
use App\Repositories\Interfaces\EBultenInterface;
use Illuminate\Http\Request;

class EBultenController extends Controller
{
    private $_bultenService;

    public function __construct(EBultenInterface $bultenService)
    {
        $this->_bultenService = $bultenService;
    }

    public function createEBulten(EbultenCreateRequest $request)
    {
        try {
            $data = $request->only('mail');
            $item = $this->_bultenService->create($data);
            if ($item) {
                return back()->with('message', 'Tebrikler ebültene başarılı şekilde kaydoldunuz');
            } else {
                return back()->withErrors('E bültene kaydolma sırasında bir hata oluştu daha sonra tekrar deneyiniz');
            }
        } catch (\Exception $exception) {
            return back()->withErrors('E bültene kaydolma sırasında bir hata oluştu daha sonra tekrar deneyiniz');
        }
    }
}
