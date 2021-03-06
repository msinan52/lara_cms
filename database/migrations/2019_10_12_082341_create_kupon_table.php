<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKuponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kuponlar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 20);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->unsignedSmallInteger('qty');
            $table->unsignedSmallInteger('active');
            $table->decimal('discount_price', 8, 2);
            $table->unsignedInteger('min_basket_price');

        });

        Schema::create('kuponlar_kategori', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('coupon_id');
            $table->unsignedInteger('category_id');

            $table->foreign('coupon_id')->references('id')->on('kuponlar')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('kategoriler')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kuponlar');
    }
}
