<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAyarlarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ayarlar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 40);
            $table->string('desc', 255);
            $table->string('domain', 40);
            $table->string('logo', 100)->nullable();
            $table->string('footer_logo', 100)->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('keywords', 100)->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('twitter', 100)->nullable();
            $table->string('instagram', 100)->nullable();
            $table->string('youtube', 100)->nullable();
            $table->string('footer_text', 250)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('mail', 50)->nullable();
            $table->string('adres', 150)->nullable();
            $table->boolean('active')->default(1);
            $table->decimal('cargo_price', 8, 2)->default(10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ayarlar');
    }
}
