<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Users
        $users = User::all();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            return;
        }

        // Published Posts
        $publishedPosts = Post::factory(20)
            ->published()
            ->recycle($users)
            ->create();

        // Draft Posts
        $draftPosts = Post::factory(8)
            ->draft()
            ->recycle($users)
            ->create();

        // All Posts
        $allPosts = $publishedPosts->merge($draftPosts);
        
        $allPosts->each(function ($post) use ($categories) {
            // Add random categories to each post
            $randomCategories = $categories->random(rand(1, min(3, $categories->count())));
            $post->categories()->attach($randomCategories->pluck('id'));
            
            // Add cover image to some posts (70% chance)
            if (rand(1, 100) <= 70) {
                $this->addRandomCoverImage($post);
            }
        });
    }
    
    /**
     * Add a random cover image to the post
     */
    private function addRandomCoverImage(Post $post): void
    {
        $imageUrls = [
            'https://picsum.photos/800/400?random=' . $post->id,
            'https://picsum.photos/800/400?random=' . ($post->id + 100),
            'https://picsum.photos/800/400?random=' . ($post->id + 200),
        ];
        
        $randomImageUrl = $imageUrls[array_rand($imageUrls)];
        
        try {
            $imageContent = file_get_contents($randomImageUrl);
            if ($imageContent !== false) {
                $tempPath = storage_path('app/temp/' . uniqid() . '.jpg');
                
                if (!file_exists(dirname($tempPath))) {
                    mkdir(dirname($tempPath), 0755, true);
                }
                
                file_put_contents($tempPath, $imageContent);
                
                $post->addMedia($tempPath)
                    ->toMediaCollection('cover_images');
                
                unlink($tempPath);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to add cover image to post {$post->id}: " . $e->getMessage());
        }
    }
} 