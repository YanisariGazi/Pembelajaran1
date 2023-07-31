<?php

namespace App\Http\Controllers;
use App\Models\Like;
use App\Models\Report;
use App\Models\Artikel;
use App\Models\Dislike;
use Illuminate\Http\Request;
use App\Mail\TakedownArtikel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ArtikelController extends Controller
{
    
    public function createArtikel(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'poto' => 'required|max:1024',
            'nama_kuliner' => 'required|string',
            'daerah' => 'required|string',
            'deskripsi' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ]);
        }
        
        $user = auth()->user();
        $user_id = $user->id;
        
        $file = $request->file('poto');
        $image = time() . rand(100, 999) . $file->getClientOriginalName();
        $uploadedImage = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'Kuliner',
            'public_id' => $image  // Gunakan nama file yang sama di Cloudinary
        ]);
        
        $artikel = Artikel::create([
            'user_id' => $user_id,
            'poto' => $uploadedImage->getSecurePath(),
            'nama_kuliner' => $request->nama_kuliner,
            'daerah' => $request->daerah,
            'deskripsi' => $request->deskripsi,
        ]);
        
        return response()->json([
            'message' => 'Artkel Terkirim',
            'Artikel' => $artikel
        ]); 
    }

    public function showArtikel(){
        //show semua produk dari 1 user
        $users = auth()->user();
        $user = $users->id;

        // $artikel = Artikel::where('user_id', $user)
        //     ->with(['users' => function ($query)
        //     {
        //         $query->select('id', 'name', 'image');
        //     }
        //     ])->get();
//atau menggunkan ini
        $artikel = Artikel::join('users', 'users.id', '=', 'artikels.user_id')
                    ->where('artikels.user_id', $user)
                    ->select('artikels.*', 'users.id', 'users.name', 'users.image')
                    ->get();
        
        return response()->json([
            'Artikel' => $artikel
        ]);  
    }

    public function showOneArtikel($id){
    //show satu artikel dari punya semua orang
        // $artikel = Artikel::where('id', $id)->get();

//atau menggunakan 
        $artikel = Artikel::join('users', 'artikels.user_id', '=', 'users.id')
                    ->where('artikels.id', $id)
                    ->select('artikels.*', 'users.*')
                    ->get();

        return response()->json([
            'Artikel' => $artikel
        ]);  
    }

    public function showAllArtikel(){
    //show semua artikel semua orang
        // $artikel = Artikel::with(['users' => function ($query) {
        //     $query->select('id', 'name', 'image');
        // }])->get();

// atau menggunakan cara ini

        $artikel = Artikel::join('users', 'artikels.user_id', '=', 'users.id')
                    ->select('artikels.*', 'users.id', 'users.name', 'users.image')
                    ->get();

        return response()->json([
            'Artikel' => $artikel
        ]);
    }

    public function updateArtikel(Request $request, $id)
    {
        $users = auth()->user();
        $user = $users->id;
        $artikel = Artikel::where('id', $id)->where('user_id', $user)->first();

        if (!$artikel) {;
            return response()->json([
                'message' => 'Artikel Ini Bukan Milik Anda'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'poto' => 'required',
            'nama_kuliner' => 'required|string',
            'daerah' => 'required|string',
            'deskripsi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('poto')) {
            // Menghapus foto lama jika ada
            if ($artikel->poto) {
                $publicId = pathinfo($artikel->poto, PATHINFO_FILENAME);
                Cloudinary::destroy($publicId);
            }

            // Upload gambar baru ke Cloudinary
            $file = $request->file('poto');
            $image = time() . rand(100, 999) . $file->getClientOriginalName();
            $uploadedImage = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'Kuliner',
                'public_id' => $image // Gunakan nama file yang sama di Cloudinary
            ]);

            $newArtikel = [
                'poto' => $uploadedImage->getSecurePath(),
                'nama_kuliner' => $request->nama_kuliner,
                'daerah' => $request->daerah,
                'deskripsi' => $request->deskripsi,
            ];

            $artikel->update($newArtikel);

            return response()->json([
                'message' => 'Artikel Berhasil Di Update',
                'Artikel' => $newArtikel
            ]);
        }

        return response()->json([
            'message' => 'Tidak ada perubahan pada artikel'
        ]);
    }

    public function deleteArtikel($id){
        $users = auth()->user();
        $user = $users->id;
        $artikel = Artikel::where('id', $id)->where('user_id', $user)->first();

        if (!$artikel) {
            return response()->json([
                'message' => 'Artikel Ini Bukan Milik Anda'
            ]);
        }else{

            Artikel::destroy($id);

            return response()->json([
                'Artikel' => 'Artikel Ini Berhasil DiHapus'
            ]);
        }
    }

    
    public function likeUnlikeArtikel($id)
    {
        $user = auth()->user();
        $userId = $user->id;

        $Artikel = Artikel::findOrFail($id);

        $like = Like::where('artikel_id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            // Jika user sudah melakukan like sebelumnya, hapus like sebelumnya dari database
            $like->delete();
            $Artikel->decrement('likes_count');

            return response()->json(['message' => 'Anda sudah melakukan unlike pada Artikel ini.']);
        } else {
            // Jika user belum melakukan like sebelumnya, tambahkan like baru
            $like = new Like();
            $like->user_id = $userId;
            $like->artikel_id = $id;
            $like->type = 'like';
            $like->save();
            $Artikel->increment('likes_count');

            return response()->json(['message' => 'Artikel ini berhasil di Like']);
        }
    }


    public function dislikeUndisArtikel($id)
    {
        $user = auth()->user();
        $userId = $user->id;

        $artikel = Artikel::findOrFail($id);

        $like = Like::where('artikel_id', $id)
            ->where('user_id', $userId)
            ->first();

        $dislike = Dislike::where('artikel_id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            // Jika user sudah melakukan like sebelumnya, hapus like dan tambahkan dislike
            $artikel->likes_count -= 1;
            $artikel->save();
            $like->delete();

            if ($dislike) {
                // Jika user sudah melakukan dislike sebelumnya, hapus dislike dari database
                $dislike->delete();
                $artikel->decrement('dislikes_count');
            } else {
                // Jika user belum melakukan dislike sebelumnya, tambahkan dislike baru
                $dislike = new Dislike();
                $dislike->user_id = $userId;
                $dislike->artikel_id = $id;
                $dislike->type = 'dislike';
                $dislike->save();
                $artikel->increment('dislikes_count');
            }

            return response()->json(['message' => 'Anda telah mengubah like menjadi dislike pada Artikel ini.']);
        } elseif ($dislike) {
            // Jika user sudah melakukan dislike sebelumnya, hapus dislike dari database
            $dislike->delete();
            $artikel->decrement('dislikes_count');

            return response()->json(['message' => 'Anda sudah melakukan undislike pada Artikel ini.']);
        } else {
            // Jika user belum melakukan like dan dislike sebelumnya, tambahkan dislike baru
            $dislike = new Dislike();
            $dislike->user_id = $userId;
            $dislike->artikel_id = $id;
            $dislike->type = 'dislike';
            $dislike->save();
            $artikel->increment('dislikes_count');

            return response()->json(['message' => 'Artikel ini berhasil di dislike']);
        }
    }

    public function reportArtikel(Request $request, $id){
        
        $artikel = Artikel::where('id', $id)->first();
        $users = auth()->user();
        $report = Report::where('user_id', $users->id)->where('artikel_id', $id)->first();

        $validator = Validator::make($request->all(), [
            'reporter' => 'required|string',
            'reasonReport' => 'required|string' 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ]);
        }

        if($report){
            return response()->json([
                "Message" => "Anda sudah melaporkan Artikel ini",
            ]);
        }else{
            $reportArtikel = new Report();
            $reportArtikel->user_id =  $users->id;
            $reportArtikel->artikel_id = $id;
            $reportArtikel->reporter = $request->reporter;
            $reportArtikel->reasonReport = $request->reasonReport;
            $reportArtikel->status = false;
            $reportArtikel->save();
            $artikel->increment('report');

            if($artikel->report > 2){
                $artikel->delete();
                
                Mail::to($users->email)->send(new TakedownArtikel($artikel, $users));
                return response()->json([
                    "Message" => "takedown Artikel"
                ]);
                
            }


            return response()->json([
                "Message" => "Your complaint has been sent to the admin",
                "Report" => $reportArtikel
            ]);
            }

    }

    
}
