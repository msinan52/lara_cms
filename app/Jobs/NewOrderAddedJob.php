<?php

namespace App\Jobs;

use App\Mail\NewOrderAdminNotificationMail;
use App\Mail\NewUserOrderAddedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class NewOrderAddedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $_user, $_order, $_basketItems, $_email, $_prices;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $user, $order, $basketItems, $prices)
    {
        $this->_user = $user;
        $this->_order = $order;
        $this->_basketItems = $basketItems;
        $this->_email = $email;
        $this->_prices = $prices;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->_email)->send(new NewUserOrderAddedMail($this->_user, $this->_order, $this->_basketItems,$this->_prices));
        Mail::to(env('MAIL_USERNAME'))->send(new NewUserOrderAddedMail($this->_user, $this->_order, $this->_basketItems,$this->_prices));
//        Mail::to(env('MAIL_USERNAME'))->send(new NewOrderAdminNotificationMail($this->_user, $this->_order, $this->_basketItems));
    }
}
