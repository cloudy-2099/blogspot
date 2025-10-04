<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserFollowed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $follower;
    public $followed;

    public function __construct(User $follower, User $followed)
    {
        $this->follower = $follower;
        $this->followed = $followed;
    }

    public function broadcastOn()
    {
        // Kirim hanya ke user yang di-follow
        return new PrivateChannel('user.' . $this->followed->id);
    }
    public function broadcastAs()
    {
        return 'UserFollowed';
    }
    public function broadcastWith()
    {
        return [
            'type' => 'follow', // 👈 tambahkan type biar di Blade bisa dibedakan
            'user' => [ // 👈 konsisten sama notifikasi like/comment
                'id'       => $this->follower->id,
                'username' => $this->follower->username,
            ],
            'follower' => [
                'id' => $this->follower->id,
                'username' => $this->follower->username,
            ],
            'followed' => [
                'id' => $this->followed->id,
                'username' => $this->followed->username,
            ],
        ];
    }
}
