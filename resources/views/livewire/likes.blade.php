<div wire:init="loadLikes">
    @if(!$readyToLoad)
        <div class="text-center my-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @else
        @forelse($receivedLikes as $like)
            <div class="mb-2 border-bottom pb-2">
                ❤️ {{ $like->user->username }} Menyukai di 
                <a href="{{ route('posts.show', $like->post->slug) }}">
                    {{ $like->post->title }}
                </a>:
                <blockquote class="mb-0">
                    {{ Str::limit($like->body, 100) }}
                </blockquote>
            </div>
        @empty
            <p class="text-muted">Belum ada yang menyukai</p>
        @endforelse

        <div class="mt-3">
            {{ $receivedLikes->links() }}
        </div>
    @endif
</div>
