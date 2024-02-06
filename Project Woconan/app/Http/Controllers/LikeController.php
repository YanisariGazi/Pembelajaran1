<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function like($id)
    {
        $user = auth()->user();
        $userId = $user->id;

        $post = Post::findOrFail($id);

        $like = Like::where('post_id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            // Jika user sudah melakukan like sebelumnya, hapus like sebelumnya dari database
            $like->delete();
            $post->decrement('likes_count');

            return response()->json(['message' => 'Anda sudah melakukan unlike pada post ini.']);
        } else {
            // Jika user belum melakukan like sebelumnya, tambahkan like baru
            $like = new Like();
            $like->post_id = $id;
            $like->user_id = $userId;
            $like->type = 'like';
            $like->save();
            $post->increment('likes_count');

            return response()->json(['message' => 'postingan ini berhasil di Like']);
        }
    }



    public function showLike($id)
    {
        try {
            // Mendapatkan post berdasarkan ID
            $post = Post::findOrFail($id);

            // Mendapatkan jumlah like dari post
            $likeCount = $post->likes()->count();

            // Mengembalikan respons JSON
            return response()->json(['likeCount' => $likeCount], 200);
        } catch (\Exception $e) {
            // Jika post tidak ditemukan, mengembalikan respons error
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }
    }



}




