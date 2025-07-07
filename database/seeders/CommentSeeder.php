<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Users
        $users = User::all();
        $posts = Post::published()->get();

        if ($users->isEmpty() || $posts->isEmpty()) {
            return;
        }

        // Parent Comments
        $parentComments = Comment::factory(15)
            ->approved()
            ->recycle($users)
            ->state(fn() => ['post_id' => $posts->random()->id])
            ->create();

        // Pending Comments
        Comment::factory(8)
            ->pending()
            ->recycle($users)
            ->state(fn() => ['post_id' => $posts->random()->id])
            ->create();

        // Reply Comments
        foreach ($parentComments->take(8) as $parent) {
            Comment::factory(rand(1, 4))
                ->approved()
                ->reply($parent)
                ->recycle($users)
                ->state(['post_id' => $parent->post_id])
                ->create();
        }

        // Rejected Comments
        Comment::factory(5)
            ->rejected()
            ->recycle($users)
            ->state(fn() => ['post_id' => $posts->random()->id])
            ->create();
    }
} 