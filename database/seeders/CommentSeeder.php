<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '5551234567',
            'role_id' => 1, // Admin role
            'password' => Hash::make('password'),
        ]);

        // Create writer user
        $writer = User::firstOrCreate([
            'email' => 'writer@example.com'
        ], [
            'first_name' => 'Writer',
            'last_name' => 'User',
            'phone' => '5559876543',
            'role_id' => 2, // Writer role
            'password' => Hash::make('password'),
        ]);

        // Test users oluştur
        $users = User::factory(5)->create();

        // Test posts oluştur
        $posts = Post::factory(5)
            ->published()
            ->recycle([$admin, $writer])
            ->create();

        // Post'ları kategorilerle ilişkilendir
        $categories = \App\Models\Category::all();
        $posts->each(function ($post) use ($categories) {
            // Her post'a 1-3 arası random kategori ekle
            $randomCategories = $categories->random(rand(1, min(3, $categories->count())));
            $post->categories()->attach($randomCategories->pluck('id'));
        });

        // Ana yorumlar oluştur
        $parentComments = Comment::factory(10)
            ->approved()
            ->recycle([$admin, $writer, ...$users])
            ->state(fn() => ['post_id' => $posts->random()->id])
            ->create();

        // Bekleyen yorumlar oluştur
        Comment::factory(5)
            ->pending()
            ->recycle($users)
            ->state(fn() => ['post_id' => $posts->random()->id])
            ->create();

        // Yanıt yorumlar oluştur
        foreach ($parentComments->take(5) as $parent) {
            Comment::factory(rand(1, 3))
                ->approved()
                ->reply($parent)
                ->recycle($users)
                ->state(['post_id' => $parent->post_id])
                ->create();
        }

        // Reddedilen yorumlar oluştur
        Comment::factory(3)
            ->rejected()
            ->recycle($users)
            ->state(fn() => ['post_id' => $posts->random()->id])
            ->create();
    }
} 