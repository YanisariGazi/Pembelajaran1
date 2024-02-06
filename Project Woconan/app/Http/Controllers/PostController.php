<?php

namespace App\Http\Controllers;

// use App\Models\like;
use App\Models\Post;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:admin')->only('berandaAdmin');
    //     $this->middleware('role:user')->only('berandaUser');
    // }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Mendapatkan pengguna yang terautentikasi
        $user = $request->user();
        $image = $request->file('gambar');
        $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());

        // Membuat post dengan mengatur nilai user_id dari pengguna yang terautentikasi
        $post = $user->posts()->create([
            'gambar' => $result,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'user_id' => $user->id
        ]);

        return response()->json(['message' => 'Post berhasil disimpan', 'data' => $post], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => "Post dengan id $id tidak ditemukan"], 404);
        }

        // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah pemilik postingan atau admin
        if ($request->user()->role === 'admin' || $post->user_id === $request->user()->id) {
            $gambar = $request->file('gambar');
            $gmb_brg = $post->gambar;

            if (!$gmb_brg) {
                return response()->json(['message' => "Properti gambar pada post dengan id $id tidak ditemukan"], 404);
            }

            $result = CloudinaryStorage::replace($gmb_brg, $gambar->getRealPath(), $gambar->getClientOriginalName());

            $post->judul = $request->judul;
            $post->deskripsi = $request->deskripsi;
            $post->gambar = $result;
            $post->save();

            return response()->json(['message' => "Post berhasil diupdate", "data" => $post], 200);
        }

        return response()->json(['message' => 'Maaf, kamu tidak memiliki izin untuk mengupdate postingan ini'], 403);
    }




    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => "Post dengan id $id tidak ditemukan"], 404);
        }

        // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah pemilik postingan atau admin
        if ($post->user_id === auth()->user()->id || auth()->user()->role === 'admin') {
            // Menghapus gambar dari penyimpanan
            CloudinaryStorage::delete($post->gambar);

            // Menghapus postingan
            $post->delete();

            return response()->json(['message' => "Postingan berhasil dihapus"], 200);
        }

        return response()->json(['message' => 'Maaf, kamu tidak memiliki izin untuk menghapus postingan ini'], 403);
    }



}


