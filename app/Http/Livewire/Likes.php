<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Like;

class Likes extends Component
{
    use WithPagination;

    public $userId;
    public $readyToLoad = false;

    protected $paginationTheme = 'bootstrap';

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function loadLikes()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        $receivedLikes = $this->readyToLoad
            ? Like::whereHas('post', fn($q) => $q->where('user_id', $this->userId))
            ->with('user', 'post')
            ->latest()
            ->paginate(5)
            : collect();

        return view('livewire.likes', compact('receivedLikes'));
    }
}
