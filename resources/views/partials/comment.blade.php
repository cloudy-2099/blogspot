<li class="list-group-item"
    data-id="{{ $comment->id }}"
    data-username="{{ $comment->user->username }}"
    data-level="{{ $level }}">

    <div class="d-flex justify-content-between align-items-start">
        <div class="comment-body">
            <strong>{{ $comment->user->username }}</strong>:
            <span class="comment-text">{{ $comment->body }}</span>
            <div class="edit-container"></div> {{-- ✅ wadah khusus edit --}}
        </div>

        <div class="comment-actions ms-2">
            <button type="button" class="btn btn-sm btn-link reply-toggle">Reply</button>

            @if(auth()->id() === $comment->user_id)
                <button type="button" class="btn btn-sm btn-link edit-comment">Edit</button>
            @endif

            @if(auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Yakin ingin menghapus komentar ini?')"
                            class="btn btn-sm btn-link text-danger">
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ✅ Reply form (disembunyikan) --}}
    <form class="reply-form d-none mt-2" action="{{ route('comments.reply', $comment->id) }}" method="POST">
        @csrf
        <textarea name="body" rows="2" class="form-control" placeholder="Tulis balasan..."></textarea>
        <button type="submit" class="btn btn-primary btn-sm mt-1">Kirim</button>
        <button type="button" class="btn btn-sm btn-secondary cancel-reply">Batal</button> {{-- ✅ diganti --}}
    </form>

    {{-- ✅ List replies (hanya render jika level < 2) --}}
    @if($level < 2 && $comment->replies->count())
        <ul class="list-group mt-2 replies">
            @foreach($comment->replies as $reply)
                @include('partials.comment', [
                    'comment' => $reply,
                    'level' => $level + 1,
                    'post' => $post
                ])
            @endforeach
        </ul>
    @elseif($level < 2)
        <ul class="list-group mt-2 replies"></ul>
    @endif
</li>
