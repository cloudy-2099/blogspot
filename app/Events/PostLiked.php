<?php

namespace App\Events;

use App\Models\Like;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostLiked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $like;

    public function __construct(Like $like)
    {
        $this->like = $like->load('user', 'post');
    }

    public function broadcastOn()
    {
        // private channel khusus untuk pemilik post
        return new PrivateChannel('user.' . $this->like->post->user_id);
    }
    public function broadcastAs()
    {
        return 'PostLiked';
    }
    public function broadcastWith()
    {
        return [
            'like' => [
                'id' => $this->like->id,
                'body' => $this->like->body,
                'user' => [
                    'id' => $this->like->user->id,
                    'username' => $this->like->user->username,
                ],
                'post' => [
                    'id' => $this->like->post->id,
                    'title' => $this->like->post->title,
                    'url' => route('post.show', $this->like->post), // 👈 tambahkan
                ],
            ]
        ];
    }
}
