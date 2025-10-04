<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment)
    {
        // Pastikan relasi sudah dimuat supaya data lengkap
        $this->comment = $comment->load(['user', 'post']);
    }

    /**
     * Tentukan channel broadcast.
     * Private channel untuk pemilik post.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->comment->post->user_id);
    }


    public function broadcastAs()
    {
        return 'CommentAdded';
    }
    /**
     * Data yang dikirim ke frontend.
     */
    public function broadcastWith()
    {
        return [
            'comment' => [
                'id'   => $this->comment->id,
                'body' => $this->comment->body,
                'user' => [
                    'id'       => $this->comment->user->id,
                    'username' => $this->comment->user->username,
                ],
                'post' => [
                    'id'    => $this->comment->post->id,
                    'title' => $this->comment->post->title,
                    'url'   => route('post.show', $this->comment->post),
                ],
            ]
        ];
    }
}
