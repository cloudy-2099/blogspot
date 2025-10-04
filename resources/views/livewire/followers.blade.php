<div wire:init="loadFollowers">
    @if(!$readyToLoad)
        <div class="text-center my-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @else
        @forelse($followers as $follower)
            <div class="mb-2 border-bottom pb-2">
                👤
                <a href="{{ route('profile.show', $follower->username) }}">
                 {{ $follower->username }} </a>
            </div>
        @empty
            <p class="text-muted">Belum ada komentar</p>
        @endforelse

        <div class="mt-3">
            {{ $followers->links() }}
        </div>
    @endif
</div>
