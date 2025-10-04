<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentAddedNotification extends Notification
{
    use Queueable;

    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment->load('user', 'post');
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // tambahkan broadcast
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'comment',
            'user' => [
                'id' => $this->comment->user->id,
                'username' => $this->comment->user->username,
            ],
            'post' => [
                'id' => $this->comment->post->id,
                'title' => $this->comment->post->title,
                'url' => route('post.show', $this->comment->post),
            ],
            'body' => $this->comment->body,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'type' => 'comment',
            'user' => [
                'id' => $this->comment->user->id,
                'username' => $this->comment->user->username,
            ],
            'post' => [
                'id' => $this->comment->post->id,
                'title' => $this->comment->post->title,
                'url' => route('post.show', $this->comment->post),
            ],
            'body' => $this->comment->body,
        ];
    }
}
