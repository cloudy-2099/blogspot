<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Follow;

class PostController extends Controller
{
    public function index()
    {
        $title = '';
        if (request('category')) {
            $category = Category::firstWhere('slug', request('category'));
            $title = ' in ' . $category->name;
        }
        if (request('author')) {
            $author = User::firstWhere('username', request('author'));
            $title = ' By ' . $author->name;
        }
        return view('posts', [
            "title" => "All Post" . $title,
            "active" => 'post',
            "posts" => Post::latest()->filter(request(['search', 'category', 'author']))->paginate(4)->withQueryString()
        ]);
    }

    public function show(Post $post)
    {
        $user = auth()->user();

        $post->load([
            'author',
            'category',
            'likes',
            'comments.user',        // hanya parent comment
            'comments.replies.user' // load reply + user
        ]);

        return view('post', [
            "title" => "Single Post",
            "active" => 'post',
            "user"   => $user, // 🔑 ini ditambahkan
            "post" => $post,
            'hasLiked' => $user ? $post->likes()->where('user_id', $user->id)->exists() : false,
        ]);
    }


    public function like(Post $post)
    {
        $user = auth()->user();

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            // sudah like → batalin
            $post->likes()->detach($user->id);
        } else {
            // belum like → tambahin
            $post->likes()->attach($user->id);
        }

        return back();
    }

    // app/Http/Controllers/PostController.php
    public function search(Request $request)
    {
        $keyword = $request->get('q');

        $posts = Post::where('title', 'like', "%$keyword%")
            ->orWhere('body', 'like', "%$keyword%")
            ->with('category', 'author')
            ->take(5)
            ->get();

        return response()->json($posts);
    }

    public function feed()
    {
        $user = auth()->user();

        // ambil post dari user yang diikuti
        $posts = Post::whereIn('user_id', $user->followings->pluck('id'))
            ->latest()
            ->get();

        return view('feed', compact('posts'));
    }
}
