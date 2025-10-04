<div wire:init="loadComments" wire:ignore.self>
    @if(!$readyToLoad)
        <div class="text-center my-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @else
        <div class="comments-container d-flex flex-column" style="min-height:380px;">
            <div class="flex-grow-1">
                @forelse($receivedComments as $comment)
                    <div class="mb-2 border-bottom pb-2">
                        💬 {{ $comment->user->username }} berkomentar di 
                        <a href="{{ route('posts.show', $comment->post->slug) }}">
                            {{ $comment->post->title }}
                        </a>:
                        <blockquote class="mb-0">
                            {{ Str::limit($comment->body, 100) }}
                        </blockquote>
                    </div>
                @empty
                    <p class="text-muted">Belum ada komentar</p>
                @endforelse
            </div>

            <div class="mt-3 pagination-wrapper sticky-bottom bg-white pt-2">
                {{ $receivedComments->links() }}
            </div>
        </div>
    @endif
</div>
