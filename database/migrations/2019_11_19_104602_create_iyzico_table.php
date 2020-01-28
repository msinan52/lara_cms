<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIyzicoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iyzico', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('siparis_id')->unsigned()->unique();
            $table->string('transaction_id', 20);
            $table->string('price', 15);
            $table->string('paidPrice', 15);
            $table->string('installment', 5);
            $table->string('paymentId', 15);
            $table->string('basketId', 15);
            $table->string('status', 50)->nullable();
            $table->text('iyzicoJson')->nullable();

            $table->foreign('siparis_id')->references('id')->on('siparisler')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iyzico');
    }
}
