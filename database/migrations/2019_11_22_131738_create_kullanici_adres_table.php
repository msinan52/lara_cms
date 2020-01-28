<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKullaniciAdresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kullanici_adres', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city')->unsigned();
            $table->integer('town')->unsigned();
            $table->integer('user')->unsigned();
            $table->string('title', 50);
            $table->string('name', 50);
            $table->string('surname', 50);
            $table->string('phone', 20);
            $table->unsignedSmallInteger('type')->default(1);
            $table->string('adres', 255);
            $table->timestamps();

            $table->foreign('town')->references('id')->on('towns');
            $table->foreign('city')->references('id')->on('cities');
            $table->foreign('user')->references('id')->on('kullanicilar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kullanici_adres');
    }
}
