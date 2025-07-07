<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $predefinedCategories = [
            [
                'name' => 'Teknoloji',
                'slug' => 'teknoloji',
            ],
            [
                'name' => 'Yazılım Geliştirme',
                'slug' => 'yazilim-gelistirme',
            ],
            [
                'name' => 'Web Tasarımı',
                'slug' => 'web-tasarimi',
            ],
            [
                'name' => 'Mobil Uygulamalar',
                'slug' => 'mobil-uygulamalar',
            ],
            [
                'name' => 'Tasarım',
                'slug' => 'tasarim',
            ],
            [
                'name' => 'İş Dünyası',
                'slug' => 'is-dunyasi',
            ],
            [
                'name' => 'Eğitim',
                'slug' => 'egitim',
            ],
            [
                'name' => 'Sağlık',
                'slug' => 'saglik',
            ],
            [
                'name' => 'Spor',
                'slug' => 'spor',
            ],
            [
                'name' => 'Yaşam',
                'slug' => 'yasam',
            ],
        ];

        foreach ($predefinedCategories as $category) {
            Category::create($category);
        }
    }
}
