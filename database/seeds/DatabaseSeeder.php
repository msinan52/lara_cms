<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(KategorilerTableSeeder::class);
        $this->call(UrunlerTableSeeder::class);
        $this->call(KullaniciSeeder::class);
        $this->call(UrunAttributeTableSeeder::class);
        $this->call(AyarlarTableSeeder::class);
        $this->call(CityTownTableSeeder::class);
        $this->call(SiteOwnerTableSeeder::class);
    }
}
