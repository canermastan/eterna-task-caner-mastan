<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class DeletePostsWithoutComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:delete-posts-without-comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete posts without comments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateLimit = now()->subWeek();
        $posts = Post::where('created_at', '<', $dateLimit)
            ->whereDoesntHave('comments')
            ->get();

        foreach ($posts as $post) {
            $post->delete();
        }

        $count = $posts->count();
        $this->info("Deleted $count posts without comments.");
    }
}
