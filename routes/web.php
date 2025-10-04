<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\FollowController;
use Clockwork\Support\Doctrine\Middleware;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\DashboardPostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home', [
        "title" => "home",
        "active" => "home"
    ]);
});
Route::get('/about', function () {
    return view('about', [
        "title" => "about",
        "active" => "about",
        "name" => "riki",
        "email" => "udinuhuy",
        "image" => "rizky.jpg"
    ]);
});


// routes/web.php
Route::get('/posts', [PostController::class, 'index'])->name('posts.index'); // Postingan banyak
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show'); // Postingan Khusus
Route::get('/post/{post:slug}', [PostController::class, 'show'])->name('post.show'); // Postingan Khusus

Route::get('/categories', function () {
    return view('categories', [
        'title' => 'Post Category',
        'active' => 'categories',
        'categories' => Category::all()
    ]);
});

Route::get('/search', [PostController::class, 'search'])->name('search')->middleware('guest');


Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/register', [RegisterController::class, 'index'])->middleware('guest');

Route::post('/register', [RegisterController::class, 'store']);

Route::get(
    '/dashboard',
    function () {
        return view('dashboard.index');
    }
)->middleware('auth');
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/dashboard/posts/checkSlug', [DashboardPostController::class, 'checkSlug'])->middleware('auth');
Route::resource('/dashboard/posts', DashboardPostController::class)->middleware('auth');

Route::resource('/dashboard/categories', AdminCategoryController::class)->except('show')->middleware('is_admin');
Route::resource('comments', CommentController::class);

Route::middleware('auth')->group(function () {
    Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::post('/likes/toggle/{post}', [LikeController::class, 'toggle'])->name('likes.toggle');
    Route::post('/posts/{Post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/comments', [CommentController::class, 'store'])->name('comment.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])
        ->middleware('auth')
        ->name('comments.update');
    // routes/web.php
    Route::post('/comments/{comment}/reply', [CommentController::class, 'replyStore'])
        ->middleware('auth')
        ->name('comments.reply');
    Route::post('/follow/{user}', [FollowController::class, 'toggle'])->name('follow.toggle');
    Route::get('/feed', [PostController::class, 'feed'])->name('feed');
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])
        ->name('follow.toggle')
        ->middleware('auth');

    Route::post('/notifications/read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read');
});
Broadcast::routes(['middleware' => ['auth']]);
