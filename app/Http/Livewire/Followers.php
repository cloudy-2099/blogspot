<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Followers extends Component
{
    use WithPagination;

    public $userId;
    public $readyToLoad = false;

    protected $paginationTheme = 'bootstrap';

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function loadFollowers()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        $followers = $this->readyToLoad
            ? User::whereHas('followings', fn($q) => $q->where('following_id', $this->userId))
            ->paginate(5)
            : collect();

        return view('livewire.followers', compact('followers'));
    }
}
