<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

class DataUserController extends Controller
{

    public function show()
    {
        $level = 'user'; // Ganti dengan level yang ingin Anda cari

        $dataUser = User::where('level', $level)->get();

        return response()->json([
            'data' => $dataUser
        ]);
    }

    public function showAdmin()
    {
        $level = 'admin'; // Ganti dengan level yang ingin Anda cari

        $dataUser = User::where('level', $level)->get();

        return response()->json([
            'data' => $dataUser
        ]);
    }
    public function delete(string $id)
    {
        // try {
        //     $user = User::findOrFail($id);
        
        //     if ($user) {
        //         if ($user->orders()->count() > 0) {
        //             // Pengguna memiliki pesanan, tidak dapat dihapus
        //             return response()->json(['message' => 'User cannot be deleted because they have orders.'], 400);
        //         } else {
        //             // Pengguna tidak memiliki pesanan, dapat dihapus
        //             $user->delete();
        //             return response()->json(['message' => 'User has been deleted.'], 200);
        //         }
        //     } else {
        //         // Pengguna tidak ditemukan
        //         return response()->json(['message' => 'User not found.'], 404);
        //     }
        // } catch (QueryException $e) {
        //     // Terjadi kesalahan saat menghapus pengguna
        //     return response()->json(['message' => 'Failed to delete user.'], 500);
        // }
        
            $user = User::findOrFail($id);
        
            if ($user) {
                if ($user->orders()->count() > 0) {
                    // Pengguna memiliki pesanan, tidak dapat dihapus
                    return response()->json(['message' => 'User cannot be deleted because they have orders.'], 400);
                } else {
                    // Pengguna tidak memiliki pesanan, dapat dihapus
                    $user->delete();
                    return response()->json(['message' => 'User has been deleted.'], 200);
                }
            } else {
                // Pengguna tidak ditemukan
                return response()->json(['message' => 'User not found.'], 404);
            }
        
            // Terjadi kesalahan saat menghapus pengguna
            return response()->json(['message' => 'Failed to delete user.'], 500);
    }
}
