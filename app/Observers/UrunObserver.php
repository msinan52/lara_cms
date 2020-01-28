<?php

namespace App\Observers;

use App\Models\Urun;
use App\Repositories\Interfaces\UrunlerInterface;
use Psy\Util\Str;

class UrunObserver
{

    /**
     * Handle the urun "deleted" event.
     *
     * @param \App\Models\Urun $urun
     * @return void
     */
    public function deleted(Urun $urun)
    {
        $urun->slug = str_random(20);
        $urun->save();
        Urun::clearAllActiveProductsWithKeyTitlePriceIdCache();
    }

    public function saving(Urun $urun)
    {
        Urun::clearAllActiveProductsWithKeyTitlePriceIdCache();
    }

}
