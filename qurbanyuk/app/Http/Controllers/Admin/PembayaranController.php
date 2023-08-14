<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PembayaranController extends Controller
{

    public function create(Request $request)
    {
        $request->validate([
            'atas_nama' => 'required|string|min:4',
            'nama_bank' => 'required|string|min:1',
            'no_rekening' => 'required|integer|min:6',
        ]);

        $pembayaran = Pembayaran::create([
            'atas_nama' => $request->atas_nama,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
        ]);

        return response()->json([
            'data' => $pembayaran
        ]);
    }

    public function show()
    {
        $pembayaran = Pembayaran::all();
        return response()->json([
            'data' => $pembayaran
        ]);
    }

    public function update(Request $request, string $id)
    {
        $pembayaran = Pembayaran::find($id);

        $data = [
            'atas_nama' => $request->atas_nama,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
        ];
        $pembayaran->update($data);
        return response()->json([
            'message' => 'Pembayaran Berhasil Di Update',
            'data' => $data
        ]);
    }

    public function delete(string $id)
    {
        Pembayaran::destroy($id);

        return response()->json([
            'message' => 'Pembayaran Berhasil Di Hapus'
        ]);
    }
}
