<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrunlerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urunler_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('brand_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->decimal('buying_price', 8, 2)->nullable();
            $table->string('spot', 255)->nullable();
            $table->string('code', 50)->nullable();
            $table->json('properties')->nullable();
            $table->json('oems')->nullable();
            $table->json('supported_cars')->nullable();

            $table->foreign('product_id')->references('id')->on('urunler')->onDelete('cascade');
//            $table->foreign('brand_id')->references('id')->on('markalar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('urunler_info');
    }
}
