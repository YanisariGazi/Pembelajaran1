<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProdukController extends Controller
{

    public function show()
    {
        $dataProduk = Produk::all();

        return response()->json([
            'data' => $dataProduk
        ]);
    }
    public function create(Request $request)
    {
        $request->validate([
            'produk' => 'required|string|max:255',
            'kualitas' => 'required|string|max:255',
            'harga' => 'required|string',
            'stok' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:1024',
        ]);
        
        // Mengupload gambar ke Cloudinary
        $uploadedImage = Cloudinary::upload($request->file('gambar')->getRealPath());
        
        $produk = Produk::create([
            'produk' => $request->produk,
            'kualitas' => $request->kualitas,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'gambar' => $uploadedImage->getSecurePath() //gunakan ->getSecurePath() kalau mau jdi link / getPublicId()
        ]);
        
        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'data' => $produk
        ]);
    }


    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrfail($id);
        $data = [
            'produk' => $request->produk,
            'kualitas' => $request->kualitas,
            'harga' => $request->harga,
            'stok' => $request->stok,
        ];
        $produk->update($data);
        return response()->json([
            'message' => 'Produk berhasil diupdate',
            'Data' => $data
        ]);
    }


    public function delete(string $id)
    {
        Produk::destroy($id);

        return response()->json([
            'message' => 'Berhasil Di Hapus'
        ]);
    }
}
