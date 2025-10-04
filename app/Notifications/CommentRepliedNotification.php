<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CommentRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reply;

    public function __construct(Comment $reply)
    {
        $this->reply = $reply->load(['user', 'post']);
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // tambahkan broadcast
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'reply',
            'reply_id' => $this->reply->id,
            'user' => [
                'id' => $this->reply->user->id,
                'username' => $this->reply->user->username,
            ],
            'post' => [
                'id' => $this->reply->post->id,
                'title' => $this->reply->post->title,
                'url' => route('post.show', $this->reply->post),
            ],
        ];
    }
    public function toBroadcast($notifiable)
    {
        return [
            'type' => 'reply',
            'reply_id' => $this->reply->id,
            'user' => [
                'id' => $this->reply->user->id,
                'username' => $this->reply->user->username,
            ],
            'post' => [
                'id' => $this->reply->post->id,
                'title' => $this->reply->post->title,
                'url' => route('post.show', $this->reply->post),
            ],
        ];
    }
}
