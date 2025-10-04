<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\UserFollowed;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UserFollowedNotification;

class FollowController extends Controller
{
    public function toggle(User $user)
    {
        $authUser = Auth::user();

        // tidak bisa follow diri sendiri
        if ($authUser->id === $user->id) {
            return back()->with('error', 'Tidak bisa follow diri sendiri.');
        }

        if ($authUser->isFollowing($user->id)) {
            $authUser->followings()->detach($user->id);
            $status = 'unfollowed';
        } else {
            $authUser->followings()->attach($user->id);
            $status = 'followed';

            // simpan notifikasi
            $user->notify(new UserFollowedNotification($authUser));

            // broadcast realtime
            broadcast(new UserFollowed($authUser, $user))->toOthers();
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'followers_count' => $user->followers()->count(),
            ]);
        }

        return back();
    }
}
