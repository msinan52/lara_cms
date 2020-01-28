<?php

namespace App\Mail;

use App\Models\Ayar;
use App\Models\SiteOwnerModel;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserOrderAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $order, $basketItems, $site, $owner, $prices;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $order, $basketItems, $prices)
    {
        $this->user = $user;
        $this->order = $order;
        $this->basketItems = $basketItems;
        $this->site = Ayar::getCache();
        $this->owner = SiteOwnerModel::getLast();
        $this->prices = $prices;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(config('app.name') . ' - Sipariş Bilgileri')
            ->view('emails.newUserOrder');
    }

}
