<?php

use Illuminate\Database\Seeder;
use App\Kullanici;

class KullaniciSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Kullanici::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Kullanici::create([
            'name' => "Murat",
            'surname' => "Karabacak",
            'email' => config('admin.username'),
            'password' => Hash::make(config('admin.password')),
            'is_active' => 1,
            'role_id' => \App\Models\Auth\Role::where('name', 'super-admin')->first()->id,
            'is_admin' => 1,
        ]);

        Kullanici::create([
            'name' => "Ali",
            'surname' => "Gündoğdu",
            'email' => "ali@gmail.com",
            'password' => '$2y$10$BTORQidLo2f/IjxHYhvGweE83QQ/C3.bOAx.cbj.wtLZGHVgrp4d6',
            'is_active' => 1,
            'is_admin' => 0,
        ]);
    }
}
