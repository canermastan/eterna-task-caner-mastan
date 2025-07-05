<?php

namespace App\Events;

use App\Core\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, InteractsWithBroadcasting;

    public function __construct(
        public Post $post,
        public PostStatus $previousStatus
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('posts'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'post.status.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'post' => [
                'id' => $this->post->id,
                'title' => $this->post->title,
                'slug' => $this->post->slug,
                'status' => $this->post->status->value,
                'previous_status' => $this->previousStatus->value,
                'published_at' => $this->post->published_at?->toISOString(),
                'created_at' => $this->post->created_at->toISOString(),
            ],
            'message' => "Post '{$this->post->title}' status changed from {$this->previousStatus->value} to {$this->post->status->value}"
        ];
    }
}
