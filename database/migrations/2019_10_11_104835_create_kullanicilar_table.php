<?php

use App\Models\Auth\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKullanicilarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = Role::create(['name' => 'user']);
        Schema::create('kullanicilar', function (Blueprint $table) use ($role) {
            $table->increments('id');
            $table->string('name', 30);
            $table->string('surname', 30);
            $table->string('email')->unique();
            $table->string('password', 60)->nullable();
            $table->string('activation_code', 60)->nullable();
            $table->boolean('is_active')->default(0);
            $table->boolean('is_admin')->default(0);
            $table->string('token', 200)->nullable();
            $table->unsignedInteger('role_id')->index()->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kullanicilar');
    }
}
