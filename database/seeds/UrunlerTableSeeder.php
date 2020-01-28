<?php

use App\Models\Urun;
use App\Models\UrunDetay;
use Illuminate\Database\Seeder;

class UrunlerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(\Faker\Generator $faker)
    {
//        DB::table("Urunler")->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::delete("TRUNCATE TABLE kategori_urun;");
        Urun::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $categories = \App\Models\Kategori::all()->toArray();
        for ($i = 0; $i < 40; $i++) {
            $product_name = $faker->sentence(random_int(2, 4));
            $urun = Urun::create([
                'title' => $product_name,
                'slug' => str_slug($product_name),
                'desc' => $faker->sentence(100),
                'price' => $faker->randomFloat(2, 10, 100),
                'image' => "$i.jpg",
                'qty' => random_int(1, 25)
            ]);
            $category_id = $categories[array_rand($categories, 1)]['id'];
            DB::insert("insert into kategori_urun (category_id, product_id) values ($category_id,$urun->id)");
        }
    }
}
