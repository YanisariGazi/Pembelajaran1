<?php

namespace App\Http\Controllers;

use App\Models\ProfilUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ProfilUserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:admin')->only('showUserAdmin');
    //     $this->middleware('role:user')->only('showUser');
    //     $this->middleware('role:super_admin')->only('showUserAdmin');
    //     $this->middleware('role:super_admin')->only('SuperAdmin');
    // }


// GAMBAR WAJIB DI ISI
    // public function storeprofil(Request $request)
    // {
    //     $user = $request->user(); // Mendapatkan pengguna yang terautentikasi

    //     // Validasi input data
    //     $validatedData = $request->validate([
    //         'gambar' => 'required|image|mimes:jpeg,png,jpg,gif',
    //         'status' => 'nullable',
    //         'hobi' => 'nullable',
    //         'kewarganegaraan' => 'nullable',
    //         'jenis_kelamin' => 'nullable'
    //     ]);

    //     // Mengecek apakah pengguna sudah memiliki profil
    //     if ($user->profil) {
    //         $profilUser = $user->profil;
    //     } else {
    //         // Jika belum ada profil, buat profil baru dan unggah gambar
    //         $profilUser = ProfilUser::create([
    //             'user_id' => $user->id,
    //             'status' => $request->status ?? null,
    //             'hobi' => $request->hobi ?? null,
    //             'kewarganegaraan' => $request->kewarganegaraan ?? null,
    //             'jenis_kelamin' => $request->jenis_kelamin ?? null,
    //         ]);

    //         if ($request->hasFile('gambar')) {
    //             $image = $request->file('gambar');
    //             $result = CloudinaryStoragee::upload($image->getRealPath(), $image->getClientOriginalName());
    //             $profilUser->gambar = $result;
    //             $profilUser->save();
    //         }
    //     }

    //     // Menghapus profil tambahan jika ada
    //     ProfilUser::where('user_id', $user->id)->where('id', '<>', $profilUser->id)->delete();

    //     // Menghapus gambar yang diunggah jika profil gagal disimpan
    //     if (!$profilUser->exists) {
    //         if ($request->hasFile('gambar')) {
    //             CloudinaryStoragee::delete($result['public_id']);
    //         }
    //     }

    //     return response()->json(['message' => 'Profil berhasil disimpan', 'data' => $profilUser], 200);
    // }

// GAMBAR BOLEH TIDAK DI ISI
    public function storeprofil(Request $request)
    {
        $user = $request->user(); // Mendapatkan pengguna yang terautentikasi

        // Validasi input data
        $validatedData = $request->validate([
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'status' => 'nullable',
            'hobi' => 'nullable',
            'kewarganegaraan' => 'nullable',
            'jenis_kelamin' => 'nullable'
        ]);

        // Mengecek apakah pengguna sudah memiliki profil
        if ($user->profil) {
            $profilUser = $user->profil;
        } else {
            // Jika belum ada profil, buat profil baru dan unggah gambar
            $profilUser = ProfilUser::create([
                'user_id' => $user->id,
                'status' => $request->status ?? null,
                'hobi' => $request->hobi ?? null,
                'kewarganegaraan' => $request->kewarganegaraan ?? null,
                'jenis_kelamin' => $request->jenis_kelamin ?? null,
            ]);
        }

        // Menghapus profil tambahan jika ada
        ProfilUser::where('user_id', $user->id)->where('id', '<>', $profilUser->id)->delete();

        // Mengupdate gambar jika ada permintaan untuk mengganti gambar
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $result = CloudinaryStoragee::upload($image->getRealPath(), $image->getClientOriginalName());

            // Menghapus gambar sebelumnya jika ada
            if ($profilUser->gambar) {
                CloudinaryStoragee::delete($profilUser->gambar['public_id']);
            }

            $profilUser->gambar = $result;
            $profilUser->save();
        }

        return response()->json(['message' => 'Profil berhasil disimpan', 'data' => $profilUser], 200);
    }



// UPDATE HANYA NAME YANG WAJIB DI ISI & mengambil id user dari register
public function update(Request $request, $id)
{
    $user = $request->user();
    $authenticatedUserId = $user->id;

    // Validasi input data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
    ]);

    $profilUser = ProfilUser::find($id);

    // Pengecekan keberadaan profil pengguna
    if (!$profilUser) {
        return response()->json(['message' => 'Profil pengguna tidak ditemukan'], 404);
    }

    // Periksa apakah pengguna yang diautentikasi sama dengan pengguna yang akan diperbarui
    if ($profilUser->user_id !== $authenticatedUserId) {
        return response()->json(['message' => 'Anda tidak memiliki izin untuk mengupdate profil pengguna ini'], 403);
    }

    // Update data profil
    $profilUser->status = $request->status !== null ? $request->status : null;
    $profilUser->hobi = $request->hobi !== null ? $request->hobi : null;
    $profilUser->kewarganegaraan = $request->kewarganegaraan !== null ? $request->kewarganegaraan : null;
    $profilUser->jenis_kelamin = $request->jenis_kelamin !== null ? $request->jenis_kelamin : null;

    // Menghapus gambar yang diunggah jika ada perubahan gambar
    if ($request->hasFile('gambar')) {
        // Menghapus gambar sebelumnya dari CloudinaryStorage
        if ($profilUser->gambar) {
            CloudinaryStoragee::delete($profilUser->gambar);
        }

        $image = $request->file('gambar');
        // Mengunggah gambar baru ke CloudinaryStorage
        $result = CloudinaryStoragee::upload($image->getRealPath(), $image->getClientOriginalName());
        $profilUser->gambar = $result;
    } else {
        // Jika tidak ada file gambar yang diunggah, kosongkan gambar
        $profilUser->gambar = null;
    }

    // Update nama pengguna
    $user->name = $request->name;

    $profilUser->save();
    $user->save();

    return response()->json(['message' => 'Data profil berhasil diperbarui', 'data' => ['name' => $user->name, 'profil' => $profilUser]], 200);
}




// INI NGAMBIL ID NYA DARI USER YANG REGISTER & name & gambar tidak boleh dikosongkan
    // public function update(Request $request, $id)
    // {
    //     $user = $request->user();
    //     $authenticatedUserId = $user->id;



    //     // Validasi input data
    //     $validatedData = $request->validate([
    //     'name' => 'required|string|max:255',
    //         'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
    //     ]);

    //     $profilUser = ProfilUser::where('user_id', $id)->first();

    //     // Pengecekan keberadaan profil pengguna
    //     if (!$profilUser) {
    //         return response()->json(['message' => 'Profil pengguna tidak ditemukan'], 404);
    //     }

    //     // Periksa apakah pengguna yang diautentikasi sama dengan pengguna yang akan diperbarui
    //     if ($profilUser->user_id !== $authenticatedUserId) {
    //         return response()->json(['message' => 'Anda tidak memiliki izin untuk mengupdate profil pengguna ini'], 403);
    //     }

    //     // Update data profil
    //     $profilUser->status = $request->status;
    //     $profilUser->hobi = $request->hobi;
    //     $profilUser->kewarganegaraan = $request->kewarganegaraan;
    //     $profilUser->jenis_kelamin = $request->jenis_kelamin;

    //     // Menghapus gambar yang diunggah jika ada perubahan gambar
    //     if ($request->hasFile('gambar')) {
    //         $image = $request->file('gambar');

    //         // Menghapus gambar sebelumnya dari CloudinaryStorage
    //         if ($profilUser->gambar) {
    //             CloudinaryStoragee::delete($profilUser->gambar);
    //         }

    //         // Mengunggah gambar baru ke CloudinaryStorage
    //         $result = CloudinaryStoragee::upload($image->getRealPath(), $image->getClientOriginalName());
    //         $profilUser->gambar = $result;
    //     }

    //     // Update nama pengguna
    //     $user->name = $request->name;

    //     $profilUser->save();
    //     $user->save();

    //     return response()->json(['message' => 'Data profil berhasil diperbarui', 'data' => $profilUser], 200);
    // }


// INI NGAMBIL ID NYA DARI PROFIL USER
    // public function update(Request $request, $id)
    // {
    //     $user = $request->user();

    //     // Validasi input data
    //     $validatedData = $request->validate([
    //         'gambar' => 'image|mimes:jpeg,png,jpg,gif',
    //     ]);

    //     $profilUser = ProfilUser::find($id);

    //     // Pengecekan keberadaan profil pengguna
    //     if (!$profilUser) {
    //         return response()->json(['message' => 'Profil pengguna tidak ditemukan'], 404);
    //     }

    //     // Periksa apakah pengguna yang diautentikasi sama dengan pengguna yang akan diperbarui
    //     if ($profilUser->user_id !== $user->id) {
    //         return response()->json(['message' => 'Anda tidak memiliki izin untuk mengupdate profil pengguna ini'], 403);
    //     }

    //     // Update data profil
    //     $profilUser->status = $request->status;
    //     $profilUser->hobi = $request->hobi;
    //     $profilUser->kewarganegaraan = $request->kewarganegaraan;
    //     $profilUser->jenis_kelamin = $request->jenis_kelamin;

    //     // Menghapus gambar yang diunggah jika ada perubahan gambar
    //     if ($request->hasFile('gambar')) {
    //         $image = $request->file('gambar');

    //         // Menghapus gambar sebelumnya dari CloudinaryStorage
    //         if ($profilUser->gambar) {
    //             CloudinaryStorage::delete($profilUser->gambar);
    //         }

    //         // Mengunggah gambar baru ke CloudinaryStorage
    //         $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());
    //         $profilUser->gambar = $result;
    //     }

    //     $profilUser->save();

    //     return response()->json(['message' => 'Data profil berhasil diperbarui', 'data' => $profilUser], 200);
    // }




// MELIHAT PROFIL USER, ADMIN & SUPER_ADMIN
    public function showProfil($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Cek peran pengguna yang sedang diautentikasi
        $authenticatedUser = Auth::user();

        // Kondisi 1: User tidak bisa melihat profil admin dan profil super admin, namun bisa melihat profil user
        if ($authenticatedUser->role === 'user') {
            if ($user->role === 'admin' || $user->role === 'super_admin') {
                return response()->json(['message' => 'Access denied'], 403);
            }

            $profilUser = $user->profil;

            $data = [
                'gambar' => $profilUser ? $profilUser->gambar : null,
                'name' => $user->name,
                'status' => $profilUser ? $profilUser->status : null,
                'hobi' => $profilUser ? $profilUser->hobi : null,
                'kewarganegaraan' => $profilUser ? $profilUser->kewarganegaraan : null,
                'jenis_kelamin' => $profilUser ? $profilUser->jenis_kelamin : null
            ];

            return response()->json(['message' => 'Profil berhasil ditemukan', 'data' => $data], 200);
        }

        // Kondisi 2: Admin bisa melihat profil admin dan profil user namun tidak bisa melihat profil super admin
        if ($authenticatedUser->role === 'admin') {
            if ($user->role === 'super_admin') {
                return response()->json(['message' => 'Access denied'], 403);
            }

            $profilUser = $user->profil;

            $data = [
                'gambar' => $profilUser ? $profilUser->gambar : null,
                'name' => $user->name,
                'status' => $profilUser ? $profilUser->status : null,
                'hobi' => $profilUser ? $profilUser->hobi : null,
                'kewarganegaraan' => $profilUser ? $profilUser->kewarganegaraan : null,
                'jenis_kelamin' => $profilUser ? $profilUser->jenis_kelamin : null
            ];

            return response()->json(['message' => 'Profil berhasil ditemukan', 'data' => $data], 200);
        }

        // Kondisi 3: Super admin bisa melihat profil admin dan profil user
        if ($authenticatedUser->role === 'super_admin') {
            $profilUser = $user->profil;

            $data = [
                'gambar' => $profilUser ? $profilUser->gambar : null,
                'name' => $user->name,
                'status' => $profilUser ? $profilUser->status : null,
                'hobi' => $profilUser ? $profilUser->hobi : null,
                'kewarganegaraan' => $profilUser ? $profilUser->kewarganegaraan : null,
                'jenis_kelamin' => $profilUser ? $profilUser->jenis_kelamin : null
            ];

            return response()->json(['message' => 'Profil berhasil ditemukan', 'data' => $data], 200);
        }

        // Jika tidak memenuhi kriteria di atas, kembalikan respons error
        return response()->json(['message' => 'Maaf, kamu tidak memiliki izin untuk mengakses halaman ini'], 403);
    }



// MELIHAT USER UNTUK ADMIN PER ID
    // public function showUserAdmin($userId)
    // {
    //     $authenticatedUser = Auth::user();

    //     if ($authenticatedUser->role !== 'admin' && $authenticatedUser->role !== 'super_admin') {
    //         return response()->json(['message' => 'Access denied'], 403);
    //     }

    //     $user = User::findOrFail($userId);

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     // Cek jika pengguna yang diminta adalah admin atau super_admin
    //     if ($user->role === 'admin' || $user->role === 'super_admin') {
    //         return response()->json(['message' => 'Access denied'], 403);
    //     }

    //     $data = [
    //         'name' => $user->name,
    //         'role' => $user->role,
    //         'email' => $user->email,
    //         'no_hp' => $user->no_hp,
    //         'pekerjaan' => $user->pekerjaan,
    //         'alamat_lengkap' => $user->alamat_lengkap
    //     ];

    //     return response()->json(['message' => 'Profil berhasil ditemukan', 'data' => $data], 200);
    // }


// MELIHAT SEMUA USER YANG REGISTER
    public function showUserAdmin()
    {
        $authenticatedUser = Auth::user();

        if ($authenticatedUser->role !== 'admin' && $authenticatedUser->role !== 'super_admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $users = User::all();

        $data = [];

        foreach ($users as $user) {
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                $data[] = [
                    'name' => $user->name,
                    'role' => $user->role,
                    'email' => $user->email,
                    'no_hp' => $user->no_hp,
                    'pekerjaan' => $user->pekerjaan,
                    'alamat_lengkap' => $user->alamat_lengkap
                ];
            }
        }

        return response()->json(['message' => 'Daftar pengguna berhasil ditemukan', 'data' => $data], 200);
    }



}
