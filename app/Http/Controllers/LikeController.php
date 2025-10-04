<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Events\PostLiked;
use App\Notifications\PostLikedNotification;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    public function toggle($postId)
    {
        $post = Post::findOrFail($postId);
        $user = auth()->user();

        // pastikan ada user yang melakukan like (auth)
        if (!$user) {
            return redirect()->route('login');
        }

        $existingLike = $post->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            // hapus like
            $existingLike->delete();
        } else {
            // buat like baru
            $like = $post->likes()->create([
                'user_id' => $user->id,
            ]);

            // broadcast event real-time (ke pemilik post)
            broadcast(new PostLiked($like))->toOthers();

            // kirim notifikasi ke owner post — DENGAN PENCEGAHAN NULL
            // gunakan relasi yang benar: user() atau author()
            $owner = $post->user ?? $post->author ?? null;

            if ($owner && $owner->id !== $user->id) { // jangan notify kalau owner sendiri yang like
                try {
                    $owner->notify(new PostLikedNotification($like));
                } catch (\Throwable $ex) {
                    // jangan crash aplikasi kalau notify gagal
                    Log::error('Notify failed: ' . $ex->getMessage());
                }
            }
        }

        return back();
    }
}
