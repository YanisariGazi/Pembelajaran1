<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTimeZone;
use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('role:admin')->only('berandaAdmin');
    //     $this->middleware('role:user')->only('berandaUser');
    //     $this->middleware('role:super_admin')->only('berandaSuperAdmin');
    // }


    public function berandaUser()
    {
        // Ambil postingan terbaru dari semua pengguna
        $posts = Post::whereNotNull('gambar')
            ->orderBy('id', 'desc')
            ->get();

        // Format ulang data postingan
        $formattedPosts = $posts->map(function ($post) {
            $created_at = Carbon::parse($post->created_at)->setTimezone(new DateTimeZone('Asia/Jakarta'));
            $updated_at = Carbon::parse($post->updated_at)->setTimezone(new DateTimeZone('Asia/Jakarta'));

            return [
                'id' => $post->id,
                'user_id' => $post->user_id,
                'gambar' => $post->gambar,
                'created_at' => $created_at->format('d-m-Y H:i:s'),
                'updated_at' => $updated_at->format('d-m-Y H:i:s'),
            ];
        })->values();

        $response = [
            'message' => 'Success',
            'data' => $formattedPosts,
        ];

        return response()->json($response);
    }


    // public function readUser($id)
    // {
    //     $perPage = 500; // Jumlah karakter maksimum per halaman

    //     $post = Post::find($id);

    //     if (!$post) {
    //         return response()->json(['message' => 'Post not found'], 404);
    //     }

    //     $content = $post->content;
    //     $numPages = ceil(strlen($content) / $perPage);

    //     // Inisialisasi array kosong untuk menyimpan postingan yang akan ditampilkan
    //     $paginatedPosts = [];

    //     // Jika postingan memiliki lebih dari satu halaman, pecah menjadi halaman-halaman
    //     if ($numPages > 1) {
    //         for ($i = 0; $i < $numPages; $i++) {
    //             $start = $i * $perPage;
    //             $paginatedContent = substr($content, $start, $perPage);

    //             // Tambahkan postingan yang telah dipotong ke dalam array paginatedPosts
    //             $paginatedPosts[] = new Post([
    //                 'judul' => $post->judul,
    //                 'deskripsi' => $paginatedContent,
    //             ]);
    //         }
    //     } else {
    //         // Jika postingan hanya memiliki satu halaman, tambahkan langsung ke array paginatedPosts
    //         $paginatedPosts[] = $post;
    //     }

    //     // Buat instance Paginator untuk array paginatedPosts
    //     $posts = new Paginator($paginatedPosts, $perPage);

    //     return response()->json(['message' => $posts, 'success' => true]);
    // }



    public function readUser($id)
    {
        $posts = Post::where('id', $id)->get();

        if ($posts->isEmpty()) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $data = [];

        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->id,
                'user_id' => $post->user_id,
                'gambar' => $post->gambar,
                'judul' => $post->judul,
                'deskripsi' => $post->deskripsi,
            ];
        }

        return response()->json(['message' => 'Postingan berhasil ditemukan', 'data' => $data], 200);
    }


}

