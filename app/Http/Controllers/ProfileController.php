<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Menampilkan daftar semua user
     */
    public function index()
    {
        $users = User::latest()->paginate(10); // bisa diubah jadi all() kalau mau tanpa pagination
        $title = "Daftar User";

        return view('profile.index', compact('users', 'title'));
    }

    /**
     * Menampilkan profil user berdasarkan username
     */
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        $title = "Profil " . $user->username;

        return view('profile.show', compact('user', 'title'));
    }
}
