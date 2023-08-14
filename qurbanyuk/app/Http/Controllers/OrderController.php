<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class OrderController extends Controller
{
    public function pesanan(Request $request, $id)
    {
        $users = auth()->user();
        $user = $users->id;

        $produks = Produk::find($id);
        $produk = $produks->id;

        if($produks->stok > 0){

            $request->validate([
                'nama' => 'required|string',
                'alamat' => 'required|string',
                'no_tlp' => 'required|string|min:11',
            ]);
    
            $order = Order::create([
                'id_user' => $user,
                'id_produk' => $produk,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'no_tlp' => $request->no_tlp,
            ]);
            
            $produks->stok -= 1;
            $produks->save();
        }
        else{
            return response()->json([
                'message' => 'Hewan Qurban Sudah Habis'
                ]);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Order created successfully.',
            'order' => $order,
        ]);
        
    }

    public function batalPesanan($id)
    {
        Order::destroy($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Order cancel successfully.',
        ]);
    }

    public function buktiPembayaran(Request $request, $id){
        
        $dataOrder = Order::findOrFail($id);
        
        // Hapus gambar yang ada di Cloudinary jika ada
        if ($dataOrder->image) {
            $publicId = pathinfo($dataOrder->image, PATHINFO_FILENAME);
            Cloudinary::destroy($publicId);
        }
        else{
            // Upload gambar baru ke Cloudinary
            $uploadedImage = Cloudinary::upload($request->file('image')->getRealPath());

            // Simpan nama gambar ke dalam database
            $dataOrder->image = $uploadedImage->getSecurePath();

            if($dataOrder->image !== null){
                $dataOrder->status_pembayaran	= 'Lunas';
            }
        }
            $dataOrder->save();

            return response()->json(['message' => 'Bukti Transfer Terkirim']);
        
    }

    public function riwayatPesanan(){
        $users = auth()->user();
        $user = $users->id;

        $riwayatPesanan = Order::where('id_user', $user)
            ->with(['produks' => function ($query) 
            {
                $query->select('id', 'produk', 'kualitas','harga');
            }
            ])
            ->with(['users' => function ($query) 
            {
                $query->select('id', 'email');
            }
            ])
            ->get();

        return response()->json([
            'data' => $riwayatPesanan
        ]);   
    }

}
