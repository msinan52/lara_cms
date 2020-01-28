<?php

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategorilerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kategorilerTableName = 'kategoriler';
//        DB::table($kategorilerTableName)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Kategori::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $id = DB::table($kategorilerTableName)->insertGetId(['title' => 'Elektronik', 'slug' => 'elektronik']);
        DB::table($kategorilerTableName)->insert(['title' => 'Bilgisayar', 'slug' => 'bilgisayar', 'parent_category' => $id]);
        DB::table($kategorilerTableName)->insert(['title' => 'Ses Sistemleri', 'slug' => 'ses-sistemleri', 'parent_category' => $id]);
        DB::table($kategorilerTableName)->insert(['title' => 'Televizyon', 'slug' => 'televizyon', 'parent_category' => $id]);

        $id = DB::table($kategorilerTableName)->insertGetId(['title' => 'Kitap', 'slug' => 'kitap']);
        DB::table($kategorilerTableName)->insert(['title' => 'Edebiyat Kitapları', 'slug' => 'edebiyat-kitaplari', 'parent_category' => $id]);
        DB::table($kategorilerTableName)->insert(['title' => 'Çocuk Kitaplari', 'slug' => 'cocuk-kitaplari', 'parent_category' => $id]);


        DB::table($kategorilerTableName)->insert(['title' => 'Dergi', 'slug' => 'dergi']);
        DB::table($kategorilerTableName)->insert(['title' => 'Mobilya', 'slug' => 'mobilya']);
        DB::table($kategorilerTableName)->insert(['title' => 'Dekorasyon', 'slug' => 'dekorasyon']);
        DB::table($kategorilerTableName)->insert(['title' => 'Kozmetik', 'slug' => 'kozmetik']);
        DB::table($kategorilerTableName)->insert(['title' => 'Kişisel Bakım', 'slug' => 'kisiel-bakim']);
        DB::table($kategorilerTableName)->insert(['title' => 'Giyim ve Moda', 'slug' => 'giyim-moda']);
    }
}
