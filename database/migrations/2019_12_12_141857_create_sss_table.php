<?php

use App\Models\SiteOwnerModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sss', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('desc');
            $table->boolean('active')->default(1);
            if (env('MULTI_LANG'))
                $table->unsignedSmallInteger('lang')->default(SiteOwnerModel::LANG_TR);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sss');
    }
}
