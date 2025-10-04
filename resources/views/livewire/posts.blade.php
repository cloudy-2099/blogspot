<div wire:init="loadPosts">
    @if(!$readyToLoad)
        <div class="text-center my-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @else
        @forelse($posts as $post)
            <div class="mb-2 border-bottom pb-2">
                <a href="{{ route('posts.show', $post->slug) }}">
                    {{ $post->title }}
                </a>:
                <blockquote class="mb-0">
            <p>{{ Str::limit(strip_tags($post->body), 100) }}</p>
                </blockquote>
            </div>
        @empty
            <p class="text-muted">Belum ada Postigan</p>
        @endforelse

        <div class="mt-3">
            {{ $posts->links() }}
        </div>
    @endif
</div>
