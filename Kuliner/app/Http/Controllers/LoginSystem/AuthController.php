<?php

namespace App\Http\Controllers\LoginSystem;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\SendEmailVerify;
use App\Jobs\SendEmailVerifyJob;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ]);
        }
        
        $imagePath = public_path('user.jpg');
    
        // Upload gambar ke Cloudinary dengan menggunakan nama file asli
        $uploadedImage = Cloudinary::upload($imagePath, [
            'folder' => 'ProfilKuliner'
        ]);
        
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_confirmation' => $request->password_confirmation,
            'image' => $uploadedImage->getSecurePath(),
            'role'  => 'user',
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );
        $sendEmailVerifyJob = new SendEmailVerifyJob($user, $verificationUrl);
        dispatch($sendEmailVerifyJob);

        return response()->json([
            'message' => 'Register Berhasil, Cek Email Anda'
            
        ]);

    }

    public function registerAdmin(Request $request){
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ]);
        }

        $imagePath = public_path('user.jpg');
        // Upload gambar ke Cloudinary
        $uploadedImage = Cloudinary::upload($imagePath, [
            'folder' => 'ProfilKuliner'
        ]);
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_confirmation' => $request->password_confirmation,
            'image' => $uploadedImage->getSecurePath(),
            'role'  => 'admin',
        ]);
        

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $sendEmailVerifyJob = new SendEmailVerifyJob($user, $verificationUrl);
        dispatch($sendEmailVerifyJob);

        return response()->json([
            'message' => 'Register Berhasil',
            'Data' => $user
        ]);

    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Coba otentikasi sebagai pengguna
        if (Auth::attempt($credentials)) {
            // Otentikasi pengguna berhasil
            $user = Auth::user();

            // Periksa status verifikasi email
            if ($user->email_verified_at) {
                if ($user->level === 'admin') {
                    $token = $user->createToken('AdminToken')->accessToken;
                    return response()->json(['token' => $token, 'role' => 'admin'], 200);
                } else {
                    $token = $user->createToken('UserToken')->accessToken;
                    return response()->json(['token' => $token, 'role' => 'user'], 200);
                }
            } else {
                Auth::logout();
                return response()->json(['message' => 'Email not verified, Please Verify Your Email'], 401);
            }
        }

        // Otentikasi gagal
        return response()->json(['message' => 'Email or password is incorrect.'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(
            [
                'message' => 'Logged out'
            ]
        );
    }

    public function profilUser(){
        $users = auth()->user();
        $user = $users->id;

        $profil = user::where('id', $user)->get();
        return response()->json([
            'Profil' => $profil 
        ]);
    }

    public function updateProfil(Request $request, $id){
$user = User::find($id);

if ($request->hasFile('image')) {
    // Menghapus foto lama jika ada
    if ($user->image) {
        $publicId = pathinfo($user->image, PATHINFO_FILENAME);
        Cloudinary::destroy($publicId);
    }
    // Upload gambar baru ke Cloudinary
    $uploadedImage = Cloudinary::upload($request->file('image')->getRealPath(), [
        'folder' => 'ProfilKuliner'
    ]);
    // Simpan URL gambar ke dalam database
    $user->image = $uploadedImage->getSecurePath();
}

$request->validate([
    'name' => 'required|string|min:3',
    'image' => 'required|image|mimes:jpeg,png,jpg|max:1024',
    'bio' => 'required|string',
]);

$data = [
    'name' => $request->name,
    'image' => $user->image,
    'bio' => $request->bio,
];

$user->update($data);

return response()->json([
    'message' => 'Profil berhasil diupdate',
    'Data' => $data
]);


    }

}
