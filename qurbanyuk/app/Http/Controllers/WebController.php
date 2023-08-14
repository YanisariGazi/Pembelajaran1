<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Mail\SendMessage;
use App\Models\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\SendMessage as ModelsSendMessage;

class WebController extends Controller
{
    public function sapi(){
        $produk = 'Sapi'; // Ganti dengan level yang ingin Anda cari

        $dataProduk = Produk::where('produk', $produk)->get();

        return response()->json([
            'data' => $dataProduk
        ]);
    }
    public function kambing(){
        $produk = 'Kambing'; // Ganti dengan level yang ingin Anda cari

        $dataProduk = Produk::where('produk', $produk)->get();

        return response()->json([
            'data' => $dataProduk
        ]);
        
    }

    public function message(Request $request){

        $email = [
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ];

        Mail::to('yuk.qurbanyuk@gmail.com')->send(new SendMessage($email));

        return response()->json(['message' => 'Email sent successfully']);

    }

    
}
