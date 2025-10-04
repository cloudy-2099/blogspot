<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name',
    //     'username',
    //     'email',
    //     'password',
    // ];

    protected $guarded = ['id'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'likes')->withTimestamps();
    }
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }

    public function isFollowing($userId)
    {
        return $this->followings()->where('following_id', $userId)->exists();
    }
    public function receivedLikes()
    {
        // semua like yang diterima dari postingan user ini
        return $this->hasManyThrough(
            \App\Models\Like::class,  // model tujuan
            \App\Models\Post::class,  // model perantara
            'user_id',                // foreign key di posts (author)
            'post_id',                // foreign key di likes
            'id',                     // primary key di users
            'id'                      // primary key di posts
        );
    }

    public function receivedComments()
    {
        return $this->hasManyThrough(
            \App\Models\Comment::class, // model tujuan
            \App\Models\Post::class,    // model perantara
            'user_id',                  // foreign key di posts (penulis post)
            'post_id',                  // foreign key di comments
            'id',                       // primary key di users
            'id'                        // primary key di posts
        );
    }
}
