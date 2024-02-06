<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Komentar;
use Illuminate\Http\Request;
use App\Models\BalasanKomentar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KomentarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }



    public function showJumlah($id)
    {
        try {
            // Mendapatkan post berdasarkan ID
            $post = Post::findOrFail($id);

            // Menghitung jumlah komentar dan balasan komentar dari post
            $commentCount = Komentar::where('post_id', $id)->count();
            $replyCount = BalasanKomentar::whereIn('komentar_id', function ($query) use ($id) {
                $query->select('id')
                    ->from('komentars')
                    ->where('post_id', $id);
            })->count();

            // Menghitung total jumlah komentar dan balasan komentar
            $totalCount = $commentCount + $replyCount;

            // Mengembalikan respons JSON dengan total jumlah komentar dan balasan komentar
            return response()->json([
                'total Komentar' => $totalCount
            ], 200);
        } catch (\Exception $e) {
            // Jika post tidak ditemukan, mengembalikan respons error
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }
    }



    public function showKomentar($id)
    {
        $komentars = Komentar::where('post_id', $id)->with('user')->get();
        $balasanKomentars = BalasanKomentar::whereHas('komentar', function ($query) use ($id) {
            $query->where('post_id', $id);
        })->with('user')->get();

        $data = [
            'komentars' => $komentars->map(function ($komentar) {
                return [
                    'id' => $komentar->id,
                    'potoprofil' => $komentar->user->gambar,
                    'name' => $komentar->user->name,
                    'konten' => $komentar->konten,
                    'jam' => $komentar->created_at->setTimezone('Asia/Jakarta')->toDateTimeString(),
                ];
            }),
            'balasanKomentars' => $balasanKomentars->map(function ($balasanKomentar) {
                return [
                    'id' => $balasanKomentar->id,
                    'potoprofil' => $balasanKomentar->user->gambar,
                    'name' => $balasanKomentar->user->name,
                    'konten' => $balasanKomentar->konten,
                    'jam' => $balasanKomentar->created_at->setTimezone('Asia/Jakarta')->toDateTimeString(),
                ];
            }),
        ];

        return response()->json($data);
    }




    public function storeKomentar(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'konten' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $komentar = new Komentar();
        $komentar->konten = $request->konten;
        $komentar->user_id = Auth::id();
        $komentar->post_id = $id;

        $komentar->save();

        return response()->json([
            'success' => true,
            'data' => $komentar
        ]);
    }



// tidak perlu menggunakan user_id manual dia langsung ke isi dengan sendiri sesuai user_id token yang dimasukkan
    public function balasKomentar(Request $request, $komentarId)
    {
        $validator = Validator::make($request->all(), [
            'konten' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parentKomentar = Komentar::find($komentarId);

        if (!$parentKomentar) {
            return response()->json(['message' => 'Komentar tidak ditemukan'], 404);
        }

        $loggedInUserId = auth()->user()->id;

        $balasan = new BalasanKomentar();
        $balasan->konten = $request->konten;
        $balasan->user_id = $loggedInUserId;
        $balasan->komentar_id = $komentarId;

        $balasan->save();

        return response()->json(['message' => 'Balasan komentar berhasil ditambahkan', 'balasan' => $balasan]);
    }



    // public function destroyKomentar($id)
    // {
    //     $komentar = Komentar::find($id);

    //     if (!$komentar) {
    //         return response()->json(['message' => 'Komentar tidak ditemukan'], 404);
    //     }

    //     // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah pemilik komentar
    //     if ($komentar->user_id !== auth()->user()->id) {
    //         return response()->json(['message' => 'Anda tidak memiliki izin untuk menghapus komentar ini'], 403);
    //     }

    //     $komentar->delete();

    //     return response()->json(['message' => 'Komentar berhasil dihapus']);
    // }

    public function destroyKomentar($id)
    {
        $komentar = Komentar::find($id);

        if (!$komentar) {
            return response()->json(['message' => 'Komentar tidak ditemukan'], 404);
        }

        $user = auth()->user();

        // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah admin
        if ($user->role === 'admin') {
            $komentar->delete();
            return response()->json(['message' => 'Balasan komentar berhasil dihapus']);
        }

        // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah pemilik komentar
        if ($komentar->user_id === $user->id) {
            $komentar->delete();
            return response()->json(['message' => 'Komentar berhasil dihapus']);
        }

        // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah pemilik postingan yang dikomentari
        $postingan = $komentar->postingan;
        if ($postingan && $postingan->user_id === $user->id) {
            $komentar->delete();
            return response()->json(['message' => 'Komentar berhasil dihapus']);
        }

        return response()->json(['message' => 'Anda tidak memiliki izin untuk menghapus komentar ini'], 403);
    }



    // public function destroyBalasanKomentar($id)
    // {
    //     $balasanKomentar = BalasanKomentar::find($id);

    //     if (!$balasanKomentar) {
    //         return response()->json(['message' => 'Balasan komentar tidak ditemukan'], 404);
    //     }

    //     // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah pemilik balasan komentar
    //     if ($balasanKomentar->user_id !== auth()->user()->id) {
    //         return response()->json(['message' => 'Anda tidak memiliki izin untuk menghapus balasan komentar ini'], 403);
    //     }

    //     $balasanKomentar->delete();

    //     return response()->json(['message' => 'Balasan komentar berhasil dihapus']);
    // }

    public function destroyBalasanKomentar($id)
    {
        $balasanKomentar = BalasanKomentar::find($id);

        if (!$balasanKomentar) {
            return response()->json(['message' => 'Balasan komentar tidak ditemukan'], 404);
        }

        $user = auth()->user();

        // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah admin
        if ($user->role === 'admin') {
            $balasanKomentar->delete();
            return response()->json(['message' => 'Balasan komentar berhasil dihapus']);
        }

        // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah pemilik balasan komentar
        if ($balasanKomentar->user_id === $user->id) {
            $balasanKomentar->delete();
            return response()->json(['message' => 'Balasan komentar berhasil dihapus']);
        }

        // Memeriksa apakah pengguna yang sedang melakukan permintaan adalah pemilik komentar
        $komentar = $balasanKomentar->komentar;
        if ($komentar && $komentar->user_id === $user->id) {
            $balasanKomentar->delete();
            return response()->json(['message' => 'Balasan komentar berhasil dihapus']);
        }

        return response()->json(['message' => 'Anda tidak memiliki izin untuk menghapus balasan komentar ini'], 403);
    }


}
