<?php

use App\Models\SiteOwnerModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcerikYonetimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icerik_yonetim', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->string('image', 200)->nullable();
            $table->string('slug', 130);
            $table->string('spot', 255)->nullable();
            $table->text('desc');
            $table->boolean('active')->default(true);
            $table->timestamps();
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
        Schema::dropIfExists('icerik_yonetim');
    }
}
