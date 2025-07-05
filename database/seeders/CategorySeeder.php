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
        // Basit kategoriler - sadece name ve slug
        $predefinedCategories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
            ],
            [
                'name' => 'Programming',
                'slug' => 'programming',
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
            ],
            [
                'name' => 'Mobile Apps',
                'slug' => 'mobile-apps',
            ],
            [
                'name' => 'Design',
                'slug' => 'design',
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
            ],
        ];

        // Kategorileri oluştur
        foreach ($predefinedCategories as $category) {
            Category::create($category);
        }
    }
}
