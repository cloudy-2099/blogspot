<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Comment;

class Comments extends Component
{
    use WithPagination;

    public $userId;
    public $readyToLoad = false;

    protected $paginationTheme = 'bootstrap';

    // 👇 tambahkan
    protected $queryString = [
        'page' => ['except' => 1, 'as' => 'commentsPage'],
    ];

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function loadComments()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        $receivedComments = null;

        if ($this->readyToLoad) {
            $receivedComments = Comment::whereHas('post', fn($q) => $q->where('user_id', $this->userId))
                ->with('user', 'post')
                ->latest()
                ->paginate(5, ['*'], 'commentsPage'); // 👈 custom pageName
        }

        return view('livewire.comments', compact('receivedComments'));
    }
}
