<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteOwnerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_owner_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name', 50);
            $table->string('company_address', 255);
            $table->string('phone', 30);
            $table->string('fax', 30)->nullable();
            $table->string('email', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_owner_info');
    }
}
