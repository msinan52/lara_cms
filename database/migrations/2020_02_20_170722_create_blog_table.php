<?php

use App\Models\SiteOwnerModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->string('image', 200)->nullable();
            $table->string('slug', 130);
            $table->string('tags', 200)->nullable();
            $table->text('desc')->nullable();
            $table->unsignedInteger('parent')->nullable();
            $table->boolean('active')->default(true);
            if (env('MULTI_LANG'))
                $table->unsignedSmallInteger('lang')->default(SiteOwnerModel::LANG_TR);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog');
    }
}
