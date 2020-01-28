<?php

use Illuminate\Database\Seeder;
use  App\Models\Ayar;

class AyarlarTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $config = Ayar::create([
            'title' => 'Site Default Başlık',
            'desc' => 'site default açıklama',
            'domain' => 'http://127.0.0.1::8000',
            'logo' => 'logo.png',
            'footer_logo' => 'footer_logo.png',
            'icon' => 'icon.png',
            'keywords' => 'kelime,ornek,default',
            'footer_text' => 'footer örnek yazı',
            'mail' => 'ornek@mail.com',
            'adres' => 'örnek adres bilgileri',
            'active' => 1,
        ]);
        Ayar::setCache($config);
    }
}
