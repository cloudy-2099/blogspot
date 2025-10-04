<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Comment;

class CommentDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $commentId;
    public $postOwnerId;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment)
    {
        $this->commentId   = $comment->id;
        $this->postOwnerId = $comment->post->user_id; // pemilik postingan
    }

    /**
     * Tentukan channel broadcast
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('user.' . $this->postOwnerId);
    }

    public function broadcastAs(): string
    {
        return 'CommentDeleted';
    }
}
