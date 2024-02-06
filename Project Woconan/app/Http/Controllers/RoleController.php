<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{

    public function getUserData()
    {
        $users = User::select('id', 'name', 'role', 'email')->get();

        return response()->json(['message' => 'Success', 'data' => $users]);
    }




    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Maaf, kamu bukan Super Admin'], 403);
        }

        // Jika super admin mencoba mengubah peran dirinya sendiri
        if ($user->id === $request->user()->id && $user->role === 'super_admin') {
            return response()->json(['message' => 'Maaf, kamu tidak dapat mengubah peran dirimu sendiri'], 403);
        }

        // Validasi input data
        $validatedData = $request->validate([
            'role' => 'required|in:super_admin,admin,user',
        ]);

        // Memastikan hanya super admin yang dapat mengubah menjadi super admin
        if ($validatedData['role'] === 'super_admin' && $request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Maaf, hanya super admin yang dapat mengubah menjadi super admin'], 403);
        }

        $user->role = $validatedData['role'];
        $user->save();

        return response()->json(['message' => 'Role berhasil diperbarui', 'data' => [
            'name' => $user->name,
            'role' => $user->role,
        ]]);
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'Maaf, kamu bukan super admin'], 403);
        }

        // Jika super admin mencoba menghapus dirinya sendiri
        if ($user->id === $request->user()->id && $user->role === 'super_admin') {
            return response()->json(['message' => 'Maaf, kamu tidak dapat menghapus dirimu sendiri'], 403);
        }

        // Hapus komentar pengguna
        $user->komentars()->delete();

        // Hapus profil pengguna
        $user->profil()->delete();

        // Hapus postingan pengguna
        $user->posts()->delete();

        // Hapus pengguna
        $user->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus']);
    }


    public function showUnverifiedUsers()
{
    // Pastikan hanya super admin yang dapat mengakses method ini
    if (Auth::user()->role !== 'super_admin') {
        return response()->json(['message' => 'Maaf, kamu tidak memiliki izin untuk mengakses halaman ini'], 403);
    }

    // Ambil daftar pengguna yang belum memverifikasi email
    $unverifiedUsers = User::whereNull('email_verified_at')->get();

    // Buat array untuk menyimpan data pengguna yang akan ditampilkan
    $userData = [];

    // Loop melalui setiap pengguna dan tambahkan atribut yang diinginkan ke dalam array
    foreach ($unverifiedUsers as $user) {
        $userData[] = [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'email' => $user->email
        ];
    }

    // Kembalikan respons dengan data pengguna yang telah diformat
    return response()->json(['message' => 'Berhasil', 'data' => $userData], 200);
}



}
