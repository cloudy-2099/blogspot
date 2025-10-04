<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Events\CommentAdded;
use Illuminate\Http\Request;
use App\Events\CommentReplied;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CommentAddedNotification;
use App\Notifications\CommentRepliedNotification;

class CommentController extends Controller
{

    // PostController.php
    public function show($id)
    {
        $post = Post::with(['likes.user', 'comments.user'])->findOrFail($id);
        return view('post.show', compact('post'));
    }

    public function store(Request $request, $postId)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'post_id'   => $postId,
            'user_id'   => auth()->id(),
            'body'      => $request->body,
            'parent_id' => null,
        ]);

        $comment->load(['user', 'post']);

        // broadcast ke orang lain (kecuali pengirim)
        broadcast(new CommentAdded($comment));
        $comment->load(['user', 'post.user']); // 👈 penting

        $postOwner = $comment->post->user;

        if ($postOwner && $postOwner->id !== auth()->id()) {
            Notification::send($postOwner, new CommentAddedNotification($comment));
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id'   => $comment->id,
                'body' => $comment->body,
                'user' => [
                    'id'       => $comment->user->id,
                    'username' => $comment->user->username,
                ],
                'created_at' => $comment->created_at->diffForHumans(),
            ],
            'auth_id' => auth()->id(), // ✅ tambahin ini
            'post_owner_id' => $comment->post->user_id, // ✅ supaya bisa cek kalau user pemilik post
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'body' => 'required|string|max:500'
        ]);

        $comment->update(['body' => $request->body]);

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    public function destroy(Comment $comment)
    {
        $post = $comment->post;

        // hanya pemilik komentar atau pemilik postingan yang boleh hapus
        if (auth()->id() === $comment->user_id || auth()->id() === $post->user_id) {
            $comment->delete();

            return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Anda tidak punya izin menghapus komentar ini.');
    }


    public function replyStore(Request $request, $commentId)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);
        $parent = Comment::findOrFail($commentId);

        $reply = Comment::create([
            'body'      => $request->body,
            'user_id'   => auth()->id(),
            'post_id'   => $parent->post_id,
            'parent_id' => $parent->id,
        ]);
        $reply->load(['user', 'post', 'parent']);

        // 🔹 kirim notifikasi
        if ($parent->user_id !== auth()->id()) {
            $parent->user->notify(new CommentRepliedNotification($reply));
        }

        if ($parent->post->user_id !== auth()->id() && $parent->post->user_id !== $parent->user_id) {
            $parent->post->user->notify(new CommentRepliedNotification($reply));
        }

        // 🔹 broadcast realtime ke Echo
        broadcast(new CommentReplied($reply));

        // 🔹 balikin JSON biar frontend bisa append langsung
        return response()->json([
            'success' => true,
            'reply' => [
                'id'   => $reply->id,
                'body' => $reply->body,
                'user' => [
                    'id'       => $reply->user->id,
                    'username' => $reply->user->username,
                ],
                'post' => [
                    'id'    => $reply->post->id,
                    'title' => $reply->post->title,
                    'url'   => route('post.show', $reply->post),
                ],
                'parent_id' => $reply->parent_id,
                'created_at' => $reply->created_at->diffForHumans(),
            ],
            'auth_id' => auth()->id(), // ✅
            'post_owner_id' => $reply->post->user_id, // ✅
        ]);
    }
}
