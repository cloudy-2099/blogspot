@extends('layout.main')
@section('container')

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-3">{{ $post->title }}</h2>

            <h5 class="mb-3">
                By:
                <a href="/posts?author={{ $post->author->username }}" class="text-decoration-none">
                    {{ $post->author->name }}
                </a>
                in
                <a href="/posts?category={{ $post->category->slug }}" class="text-decoration-none">
                    {{ $post->category->name }}
                </a>
            </h5>

            {{-- Image --}}
            @if($post->image)
                <div style="max-height: 400px; overflow:hidden;">
                    <img src="{{ asset('storage/'. $post->image) }}" class="img-fluid" alt="">
                </div>
            @else
                <img src="https://picsum.photos/1200/400?{{ $post->category->name }}" class="img-fluid" alt="">
            @endif

            {{-- Body --}}
            <article class="my-3">
                {!! $post->body !!}
            </article>

            {{-- Form Komentar --}}
            <form id="commentForm" action="{{ route('comments.store', $post->id) }}" method="POST" class="mt-3">
                @csrf
                <textarea name="body" rows="2" class="form-control" placeholder="Tulis komentar..."></textarea>
                <button type="submit" class="btn btn-primary mt-2">Kirim</button>
            </form>

            {{-- List Komentar --}}
            <ul class="list-group mt-3" id="commentList">
                @forelse($post->comments as $comment)
                    @include('partials.comment', ['comment' => $comment, 'level' => 0, 'post' => $post])
                @empty
                    <li class="list-group-item text-muted">Belum ada komentar</li>
                @endforelse
            </ul>

            <a href="/posts" class="btn btn-primary mt-3">Back to post</a>
        </div>
    </div>
</div>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Pusher & Echo --}}
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>

<script>
const postId = @json($post->id);
const authId = @json(auth()->id());
const postOwnerId = @json($post->user_id);
const authUsername = @json(auth()->user()->username);

$(function () {
    // Setup CSRF
    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
    });

    // Helper escape HTML
    function escapeHtml(text) {
        if (!text) return "";
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }

    // Render comment/reply ke UI
    function renderComment(c, level, isTemp = false) {
        if (level > 2) level = 2;

        let editBtn = "";
        if (c.user.id == authId) {
            editBtn = `<button type="button" class="btn btn-sm btn-link edit-comment" ${isTemp ? "disabled" : ""}>Edit</button>`;
        }

        let deleteBtn = "";
        if (c.user.id == authId || authId == postOwnerId) {
            deleteBtn = `
                <form action="/comments/${c.id}" method="POST" style="display:inline;">
                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-link text-danger delete-comment" ${isTemp ? "disabled" : ""}>Delete</button>
                </form>
            `;
        }

        return `
            <li class="list-group-item" data-id="${c.id}" data-username="${c.user.username}" data-level="${level}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="comment-body">
                        <strong>${c.user.username}</strong>:
                        <span class="comment-text">${escapeHtml(c.body)}</span>
                        <div class="edit-container"></div>
                    </div>
                    <div class="comment-actions ms-2">
                        <button type="button" class="btn btn-sm btn-link reply-toggle">Reply</button>
                        ${editBtn}
                        ${deleteBtn}
                    </div>
                </div>
                <form class="reply-form d-none mt-2" action="/comments/${c.id}/reply" method="POST">
                    <textarea name="body" rows="2" class="form-control" placeholder="Tulis balasan..."></textarea>
                    <button type="submit" class="btn btn-primary btn-sm mt-1">Kirim</button>
                    <button type="button" class="btn btn-sm btn-secondary cancel-reply">Batal</button>
                </form>
                ${level < 2 ? `<ul class="list-group mt-2 replies"></ul>` : ""}
            </li>
        `;
    }

    // Toggle reply form
    $(document).on("click", ".reply-toggle", function () {
        let li = $(this).closest("li");
        let form = li.children(".reply-form");
        if (!form.length) return;
        let username = li.data("username");
        form.toggleClass("d-none");
        if (!form.hasClass("d-none")) {
            let textarea = form.find('textarea[name="body"]');
            if (textarea.val().trim() === "") textarea.val("@" + username + " ");
            textarea.focus();
        }
    });

    // Cancel reply
    $(document).on("click", ".cancel-reply", function () {
        let form = $(this).closest(".reply-form");
        form.addClass("d-none");
        form.find("textarea").val("");
    });

    // Submit komentar baru
    $("#commentForm").on("submit", function (e) {
        e.preventDefault();
        let url = $(this).attr("action");
        let body = $(this).find("textarea").val().trim();
        if (!body) return;

        let tempId = "temp-" + Date.now();
        let c = { id: tempId, body: body, user: { id: authId, username: authUsername } };

        $("#commentList .text-muted").remove();
        $("#commentList").prepend(renderComment(c, 0, true));
        $(this).find("textarea").val("");

        $.post(url, { body: body }).done(function (res) {
            if (res.success) {
                let li = $(`li[data-id="${tempId}"]`);
                li.replaceWith(renderComment(res.comment, 0));
            } else {
                $(`li[data-id="${tempId}"]`).remove();
            }
        }).fail(() => $(`li[data-id="${tempId}"]`).remove());
    });

    // Submit reply
    $(document).on("submit", ".reply-form", function (e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr("action");
        let body = form.find("textarea").val().trim();
        if (!body) return;

        let parentLi = form.closest("li");
        let parentLevel = parseInt(parentLi.data("level")) || 0;
        let newLevel = parentLevel + 1;
        if (newLevel > 2) newLevel = 2;

        let tempId = "temp-" + Date.now();
        let c = { id: tempId, body: body, user: { id: authId, username: authUsername }, parent_id: parentLi.data("id") };

        if (parentLevel >= 2) {
            parentLi.after(renderComment(c, 2, true)); // ✅ taruh sejajar
        } else {
            parentLi.children(".replies").append(renderComment(c, newLevel, true));
        }

        form.find("textarea").val("");
        form.addClass("d-none");

        $.post(url, { body: body }).done(function (res) {
            if (res.success) {
                let li = $(`li[data-id="${tempId}"]`);
                li.replaceWith(renderComment(res.reply, newLevel));
            } else {
                $(`li[data-id="${tempId}"]`).remove();
            }
        }).fail(() => $(`li[data-id="${tempId}"]`).remove());
    });

    // Listener komentar baru realtime
    window.Echo.channel(`post.${postId}`).listen(".CommentAdded", (e) => {
        const c = e.comment;
        if (!c) return;

        // ✅ Skip kalau komentar milik user sendiri (sudah dirender manual)
        if (c.user.id == authId) return;

        // ✅ Skip kalau komentar dengan ID ini sudah ada
        if ($(`li[data-id="${c.id}"]`).length) return;

        $("#commentList").prepend(renderComment(c, 0));
    });


    // Listener reply baru realtime
    window.Echo.channel(`post.${postId}`).listen(".CommentReplied", (e) => {
        const c = e.reply;
        if (!c) return;
        if ($(`li[data-id="${c.id}"]`).length) return;

        let parentLi = $(`li[data-id="${c.parent_id}"]`);
        if (parentLi.length) {
            let parentLevel = parseInt(parentLi.data("level")) || 0;
            let newLevel = parentLevel + 1;
            if (newLevel > 2) newLevel = 2;

            if (parentLevel >= 2) {
                parentLi.after(renderComment(c, 2));
            } else {
                parentLi.children(".replies").append(renderComment(c, newLevel));
            }
        }
    });

    // Delete komentar
    $(document).on("submit", "form[action^='/comments/'][method='POST']", function (e) {
        if (!$(this).find("input[name='_method'][value='DELETE']").length) return;
        e.preventDefault();
        let form = $(this);
        let li = form.closest("li");
        if (!confirm("Yakin ingin menghapus komentar ini?")) return;

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            success: function () {
                li.remove();
            },
            error: function () {
                alert("Gagal menghapus komentar");
            },
        });
    });

// Edit komentar (toggle on/off)
$(document).on("click", ".edit-comment", function () {
    let li = $(this).closest("li");
    let textSpan = li.find("> .d-flex .comment-body .comment-text"); // ambil text level ini
    let editContainer = li.find("> .d-flex .comment-body .edit-container");

    // kalau sudah ada form edit, artinya user klik lagi -> cancel edit
    if (editContainer.find(".edit-form").length) {
        textSpan.show();
        li.children(".comment-actions").show();
        editContainer.empty();
        return; // stop disini (toggle off)
    }

    let original = textSpan.text().trim();
    let formHtml = `
        <form class="edit-form mt-2">
            <textarea class="form-control" rows="2">${original}</textarea>
            <div class="mt-1">
                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                <button type="button" class="btn btn-sm btn-secondary cancel-edit">Batal</button>
            </div>
        </form>
    `;

    textSpan.hide();
    li.children(".comment-actions").hide();
    editContainer.html(formHtml); // render form edit
});


    // Submit edit
    $(document).on("submit", ".edit-form", function (e) {
        e.preventDefault();
        let form = $(this);
        let li = form.closest("li");
        let id = li.data("id");
        let newBody = form.find("textarea").val().trim();
        if (!newBody) return alert("Komentar tidak boleh kosong!");

        $.ajax({
            url: `/comments/${id}`,
            type: "PUT",
            data: { body: newBody },
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            success: function (res) {
                if (res.success) {
                    li.find(".comment-text").text(res.comment.body).show();
                    li.find(".comment-actions").show();
                    li.find(".edit-container").first().empty(); // ✅ clear
                }
            },
            error: function () { alert("Gagal mengedit komentar"); }
        });
    });

    // Batal edit
// Batal edit
$(document).on("click", ".cancel-edit", function () {
    let form = $(this).closest(".edit-form");
    let li = form.closest("li");
    li.find("> .d-flex .comment-body .comment-text").show();
    li.children(".comment-actions").show();
    li.find("> .d-flex .comment-body .edit-container").empty();
});

});
</script>

@endsection
