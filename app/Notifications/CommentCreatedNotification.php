<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CommentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment,
        public Post $post,
        public User $commenter
    ) {
        $this->queue = 'notifications';
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->email && $this->shouldSendEmail($notifiable)) {
            $channels[] = 'mail';
        }
        
        $channels[] = 'broadcast';
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $postUrl = url("/posts/{$this->post->slug}");
        
        return (new MailMessage)
            ->subject("📝 Yazınıza Yeni Yorum: {$this->post->title}")
            ->greeting("Merhaba {$notifiable->first_name}!")
            ->line("**{$this->post->title}** başlıklı yazınıza yeni bir yorum yapıldı.")
            ->line("**Yorumu yapan:** {$this->commenter->first_name} {$this->commenter->last_name}")
            ->line("**Yorum içeriği:**")
            ->line("\"{$this->comment->content}\"")
            ->action('Yorumu Görüntüle', $postUrl)
            ->salutation('İyi günler dileriz!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment_created',
            'title' => 'Yazınıza Yeni Yorum',
            'message' => "{$this->commenter->first_name} {$this->commenter->last_name} \"{$this->post->title}\" yazınıza yorum yaptı.",
            'comment_id' => $this->comment->id,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'commenter_id' => $this->commenter->id,
            'commenter_name' => $this->commenter->first_name . ' ' . $this->commenter->last_name,
            'comment_content' => $this->comment->content,
            'comment_excerpt' => substr($this->comment->content, 0, 100) . (strlen($this->comment->content) > 100 ? '...' : ''),
            'action_url' => "/posts/{$this->post->slug}",
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'comment_created',
            'title' => 'Yazınıza Yeni Yorum',
            'message' => "{$this->commenter->first_name} {$this->commenter->last_name} yazınıza yorum yaptı.",
            'comment_id' => $this->comment->id,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'commenter_name' => $this->commenter->first_name . ' ' . $this->commenter->last_name,
            'comment_excerpt' => substr($this->comment->content, 0, 100) . (strlen($this->comment->content) > 100 ? '...' : ''),
            'action_url' => "/posts/{$this->post->slug}",
            'created_at' => now()->toISOString(),
            'read_at' => null,
        ]);
    }

    /**
     * Get the notification's broadcast channel.
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel("user.{$this->comment->post->user_id}");
    }

    /**
     * Determine if email notification should be sent.
     */
    private function shouldSendEmail(object $notifiable): bool
    {
        if ($notifiable->id === $this->commenter->id) {
            return false;
        }

        // maybe we can add more conditions here
        return true;
    }

    /**
     * Get the notification's broadcast type.
     */
    public function broadcastType(): string
    {
        return 'comment.notification';
    }
}
