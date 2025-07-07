<?php

namespace App\Core\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\CommentCreatedNotification;

class NotificationService
{
    public function sendCommentNotification(Comment $comment, Post $post, User $commenter): void
    {
        // Don't send notification if commenter is the post author
       if ($post->user_id === $commenter->id) {
            return;
        }

        $postAuthor = $post->user;
        
        if ($postAuthor && $postAuthor->is_active) {
            $postAuthor->notify(new CommentCreatedNotification($comment, $post, $commenter));
        }
    }
}