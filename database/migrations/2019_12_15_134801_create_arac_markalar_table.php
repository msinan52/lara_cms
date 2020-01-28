<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAracMarkalarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arac_markalar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 20);
            $table->string('slug', 30)->unique();
            $table->string('image', 50)->nullable();
            $table->boolean('active')->default(true);
        });
        Schema::create('arac_modeller', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_marka');
            $table->string('title', 30);
            $table->string('slug', 40);
            $table->boolean('active')->default(true);

            $table->foreign('parent_marka')->references('id')->on('arac_markalar')->onDelete('cascade');
        });

        Schema::create('arac_kasalar', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_marka');
            $table->unsignedInteger('parent_model');

            $table->string('title', 20);
            $table->string('slug', 30);
            $table->boolean('active')->default(true);

            $table->foreign('parent_marka')->references('id')->on('arac_markalar')->onDelete('cascade');
            $table->foreign('parent_model')->references('id')->on('arac_modeller')->onDelete('cascade');
        });

        Schema::create('arac_model_yili', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_marka');
            $table->unsignedInteger('parent_model');
            $table->unsignedInteger('parent_kasa');

            $table->string('title', 20);
            $table->string('slug', 30);
            $table->boolean('active')->default(true);

            $table->foreign('parent_marka')->references('id')->on('arac_markalar')->onDelete('cascade');
            $table->foreign('parent_model')->references('id')->on('arac_modeller')->onDelete('cascade');
            $table->foreign('parent_kasa')->references('id')->on('arac_kasalar')->onDelete('cascade');
        });

        Schema::create('arac_motor_hacmi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_marka');
            $table->unsignedInteger('parent_model');
            $table->unsignedInteger('parent_kasa');
            $table->unsignedInteger('parent_model_yili');

            $table->string('title', 100);
            $table->string('slug', 120);
            $table->boolean('active')->default(true);

            $table->foreign('parent_marka')->references('id')->on('arac_markalar')->onDelete('cascade');
            $table->foreign('parent_model')->references('id')->on('arac_modeller')->onDelete('cascade');
            $table->foreign('parent_kasa')->references('id')->on('arac_kasalar')->onDelete('cascade');
            $table->foreign('parent_model_yili')->references('id')->on('arac_model_yili')->onDelete('cascade');
        });

        Schema::create('arac_beygir_gucu', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_marka');
            $table->unsignedInteger('parent_model');
            $table->unsignedInteger('parent_kasa');
            $table->unsignedInteger('parent_model_yili');
            $table->unsignedInteger('parent_motor_hacmi');

            $table->string('title', 20);
            $table->string('slug', 30);
            $table->boolean('active')->default(true);

            $table->foreign('parent_marka')->references('id')->on('arac_markalar')->onDelete('cascade');
            $table->foreign('parent_model')->references('id')->on('arac_modeller')->onDelete('cascade');
            $table->foreign('parent_kasa')->references('id')->on('arac_kasalar')->onDelete('cascade');
            $table->foreign('parent_model_yili')->references('id')->on('arac_model_yili')->onDelete('cascade');
            $table->foreign('parent_motor_hacmi')->references('id')->on('arac_motor_hacmi')->onDelete('cascade');
        });

        Schema::create('uyumlu_araclar', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('parent_marka');
            $table->unsignedInteger('parent_model');
            $table->unsignedInteger('parent_kasa');
            $table->unsignedInteger('parent_model_yili');
            $table->unsignedInteger('parent_motor_hacmi');

            $table->foreign('product_id')->references('id')->on('urunler')->onDelete('cascade');
            $table->foreign('parent_marka')->references('id')->on('arac_markalar')->onDelete('cascade');
            $table->foreign('parent_model')->references('id')->on('arac_modeller')->onDelete('cascade');
            $table->foreign('parent_kasa')->references('id')->on('arac_kasalar')->onDelete('cascade');
            $table->foreign('parent_model_yili')->references('id')->on('arac_model_yili')->onDelete('cascade');
            $table->foreign('parent_motor_hacmi')->references('id')->on('arac_motor_hacmi')->onDelete('cascade');

        });
        Schema::create('arac_oem_kodlari', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('marka_id');

            $table->foreign('product_id')->references('id')->on('urunler')->onDelete('cascade');
            $table->foreign('marka_id')->references('id')->on('arac_markalar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arac_markalar');
        Schema::dropIfExists('arac_modeller');
        Schema::dropIfExists('arac_kasalar');
        Schema::dropIfExists('arac_model_yili');
        Schema::dropIfExists('arac_motor_hacmi');
        Schema::dropIfExists('arac_oem_kodlari');
    }
}
