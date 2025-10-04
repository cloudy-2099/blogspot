<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommentReplied implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reply;

    public function __construct(Comment $reply)
    {
        // pastikan relasi sudah ke-load semua
        $this->reply = $reply->load(['user', 'parent', 'post']);
    }

    public function broadcastOn()
    {
        $channels = [];

        if ($this->reply->parent && $this->reply->parent->user_id) {
            $channels[] = new PrivateChannel('user.' . $this->reply->parent->user_id);
        }

        if ($this->reply->post && $this->reply->post->user_id && $this->reply->post->user_id !== $this->reply->parent->user_id) {
            $channels[] = new PrivateChannel('user.' . $this->reply->post->user_id);
        }

        return $channels;
    }
    public function broadcastAs()
    {
        return 'CommentReplied';
    }
    public function broadcastWith()
    {
        return [
            'reply' => [
                'id'   => $this->reply->id,
                'body' => $this->reply->body,
                'user' => [
                    'id'       => $this->reply->user->id,
                    'username' => $this->reply->user->username,
                ],
                'post' => [
                    'id'    => $this->reply->post->id,
                    'title' => $this->reply->post->title,
                    'url'   => route('post.show', $this->reply->post),
                ],
                'parent_id' => $this->reply->parent_id,
            ]
        ];
    }
}
