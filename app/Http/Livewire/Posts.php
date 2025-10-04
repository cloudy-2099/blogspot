<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;

class Posts extends Component
{
    use WithPagination;

    public $userId;
    public $readyToLoad = false;

    protected $paginationTheme = 'bootstrap';

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function loadPosts()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        $posts = $this->readyToLoad
            ? Post::where('user_id', $this->userId)
            ->latest()
            ->paginate(5)
            : collect();

        return view('livewire.posts', compact('posts'));
    }
}
