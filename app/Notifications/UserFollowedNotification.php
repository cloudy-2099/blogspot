<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserFollowedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $follower;

    public function __construct(User $follower)
    {
        $this->follower = $follower;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'follow',
            'user' => [ // ⬅️ ganti 'follower' jadi 'user'
                'id'       => $this->follower->id,
                'username' => $this->follower->username,
            ],
            'message' => "{$this->follower->username} mulai mengikuti Anda.",
        ];
    }


    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
