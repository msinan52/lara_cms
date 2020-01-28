<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiparislerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siparisler', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sepet_id')->unsigned()->unique();
            $table->decimal('order_price', 10, 4);
            $table->decimal('cargo_price', 8, 2);
            $table->decimal('order_total_price', 10, 4);
            $table->string('status', 30)->nullable();
            $table->ipAddress('ip_adres')->nullable();
            $table->string('full_name', 50);
            $table->string('adres', 250);
            $table->string('fatura_adres', 250);
            $table->string('phone', 15);
            $table->boolean('is_payment')->default(0);


            $table->integer('installment_count')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sepet_id')->references('id')->on('sepet')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('siparisler');
    }
}
