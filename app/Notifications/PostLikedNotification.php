<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostLikedNotification extends Notification
{
    use Queueable;

    protected $like;

    public function __construct($like)
    {
        $this->like = $like->load('user', 'post');
    }

    public function via($notifiable)
    {
        return ['database']; // simpan ke database
    }

    public function toDatabase($notifiable)
    {
        return [
            'like_id' => $this->like->id,
            'user' => [
                'id' => $this->like->user->id,
                'username' => $this->like->user->username,
            ],
            'post' => [
                'id' => $this->like->post->id,
                'title' => $this->like->post->title,
                'url' => route('post.show', $this->like->post),
            ],
        ];
    }
}
