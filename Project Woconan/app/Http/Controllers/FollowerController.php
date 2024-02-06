<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\ProfilUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class FollowerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    // public function follow(Request $request, $id)
    // {
    //     $user = Auth::user();
    //     $followerId = $user->id;

    //     $followedUser = User::findOrFail($id);

    //     // Periksa apakah pengguna mencoba mengikuti dirinya sendiri
    //     if ($followedUser->id === $user->id) {
    //         return response()->json(['message' => 'Anda tidak dapat mengikuti diri sendiri'], 403);
    //     }

    //     // Periksa apakah pengguna adalah super_admin atau admin
    //     if ($followedUser->role === 'super_admin' || $followedUser->role === 'admin') {
    //         return response()->json(['message' => 'Anda tidak dapat mengikuti pengguna ini'], 403);
    //     }

    //     $follower = $followedUser->followers()->where('follower_id', $followerId)->first();

    //     if ($follower) {
    //         // Jika user sudah melakukan follow sebelumnya, hapus follow sebelumnya dari database
    //         $followedUser->followers()->detach($followerId);
    //         $message = 'Anda sudah tidak mengikuti pengguna ini.';
    //     } else {
    //         // Jika user belum melakukan follow sebelumnya, tambahkan follow baru
    //         $profilUser = $user->profil;
    //         $followedUser->followers()->attach($followerId, [
    //             'follower_name' => $user->name,
    //             'follower_gambar' => $profilUser->gambar,
    //         ]);
    //         $message = 'Anda telah mengikuti pengguna ini.';
    //     }

    //     return response()->json(['message' => $message]);
    // }

    public function follow(Request $request, $id)
    {
        $user = Auth::user();
        $followerId = $user->id;

        $followedUser = User::findOrFail($id);

        // Periksa apakah pengguna mencoba mengikuti dirinya sendiri
        if ($followedUser->id === $user->id) {
            return response()->json(['message' => 'Anda tidak dapat mengikuti diri sendiri'], 403);
        }

        // Periksa apakah pengguna adalah super_admin atau admin
        if ($followedUser->role === 'super_admin' || $followedUser->role === 'admin') {
            return response()->json(['message' => 'Anda tidak dapat mengikuti pengguna ini'], 403);
        }

        $follower = $followedUser->followers()->where('follower_id', $followerId)->first();

        if ($follower) {
            // Jika user sudah melakukan follow sebelumnya, hapus follow sebelumnya dari database
            $followedUser->followers()->detach($followerId);
            $message = 'Anda sudah tidak mengikuti pengguna ini.';
        } else {
            // Jika user belum melakukan follow sebelumnya, tambahkan follow baru
            if ($user->profil && $user->profil->gambar) {
                $followerGambar = $user->profil->gambar;
            } else {
                $followerGambar = null;
            }

            $followedUser->followers()->attach($followerId, [
                'follower_name' => $user->name,
                'follower_gambar' => $followerGambar,
            ]);
            $message = 'Anda telah mengikuti pengguna ini.';
        }

        return response()->json(['message' => $message]);
    }



    public function getFollowerCount($userId)
    {
        $user = User::findOrFail($userId);

        // Periksa apakah pengguna adalah super_admin atau admin
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return response()->json(['message' => 'Anda tidak dapat melihat jumlah pengikut pengguna ini'], 403);
        }

        $followerCount = $user->followers()->count();

        return response()->json(['follower_count' => $followerCount]);
    }



    public function getFollowers($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return response()->json(['message' => 'Anda tidak dapat melihat pengikut pengguna ini'], 403);
        }

        $followers = $user->followers;

        $followerData = [];
        foreach ($followers as $follower) {
            $followerName = $follower->pivot->follower_name;
            $followerGambar = null;

            if ($follower->profil && $follower->profil->gambar) {
                $followerGambar = $follower->profil->gambar;
            }

            $followerData[] = [
                'name' => $followerName,
                'gambar' => $followerGambar,
            ];
        }

        return response()->json(['followers' => $followerData]);
    }

}
