<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Comment $comment,
        public Post $post
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('post.' . $this->post->id . '.comments'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'comment.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'comment' => [
                'id' => $this->comment->id,
                'content' => $this->comment->content,
                'status' => $this->comment->status,
                'status_label' => $this->comment->status,
                'parent_id' => $this->comment->parent_id,
                'created_at' => $this->comment->created_at->toISOString(),
                'created_at_human' => $this->comment->created_at->diffForHumans(),
                'children' => [],
                'user' => [
                    'id' => $this->comment->user->id,
                    'first_name' => $this->comment->user->first_name,
                    'last_name' => $this->comment->user->last_name,
                    'full_name' => $this->comment->user->full_name,
                ],
            ],
            'post_id' => $this->post->id,
            'timestamp' => now()->toISOString(),
        ];
    }
}
