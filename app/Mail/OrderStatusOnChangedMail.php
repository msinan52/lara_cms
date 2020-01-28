<?php

namespace App\Mail;

use App\Models\Ayar;
use App\Models\SiteOwnerModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusOnChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $user, $order, $basketItems, $site, $prices, $orderStatusText;

    public function __construct($user, $order, $basketItems, $prices, $orderStatusText)
    {
        $this->user = $user;
        $this->order = $order;
        $this->basketItems = $basketItems;
        $this->site = Ayar::getCache();
        $this->prices = $prices;
        $this->orderStatusText = $orderStatusText;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(config('app.name') . '- ' . $this->orderStatusText)
            ->view('emails.orderStatusChangeMail');
    }
}
