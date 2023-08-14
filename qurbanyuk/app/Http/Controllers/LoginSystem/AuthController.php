<?php

namespace App\Http\Controllers\LoginSystem;

use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendEmailVerifyJob;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AuthController extends Controller
{

    public function registerAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $level = "admin";
        $imagePath = public_path('user.jpg');
        // Upload gambar ke Cloudinary
        $uploadedImage = Cloudinary::upload($imagePath);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'level' => $level,
            'image' => $uploadedImage->getSecurePath(),
            'password' => Hash::make($request->password),
        ]);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $SendEmailVerifyJob = new SendEmailVerifyJob($user, $verificationUrl);
        dispatch($SendEmailVerifyJob);

        return response()->json(
            [
                'message' => 'Admin Registered, Please Check Your Email',
            ]
        );
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);
        $level = "user";
        $imagePath = public_path('user.jpg');
        
        $uploadedImage = Cloudinary::upload($imagePath);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'level' => $level,
            'image' => $uploadedImage->getSecurePath(),
            'password' => Hash::make($request->password),
        ]);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $SendEmailVerifyJob = new SendEmailVerifyJob($user, $verificationUrl);
        dispatch($SendEmailVerifyJob);

        return response()->json(
            [
                'message' => 'User Registered, Please Check Your Email',
            ]
        );
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
                    $token = $user->createToken('AdminToken')->plainTextToken;
                    return response()->json(['token' => $token, 'role' => 'admin'], 200);
                } else {
                    $token = $user->createToken('UserToken')->plainTextToken;
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

        $profil = User::where('id', $user)->get();
        return response()->json(
            [
                'Profil' => $profil
            ]
        );
    }

    public function updateProfil(Request $request, $id){
        $user = User::find($id);
        if($user->image){
            $publicId = pathinfo($user->image, PATHINFO_FILENAME);
            Cloudinary::destroy($publicId);
        }
             // Upload gambar baru ke Cloudinary
             $uploadedImage = Cloudinary::upload($request->file('image')->getRealPath());
             // Simpan nama gambar ke dalam database
             $user->image = $uploadedImage->getSecurePath();

             $request->validate([
                'name' => 'required|string|min:3',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:1024',
                'bio' => 'required|string',
                'jenkel' => 'required|string',
             ]);

            $data = [
                'name' => $request->name,
                'image' => $user->image,
                'bio' => $request->bio,
                'jenkel' => $request->jenkel
            ];

        $user->update($data);

        return response()->json([
            'message' => 'Profil berhasil diupdate',
            'Data' => $data
        ]);
    }

    public function redirectToGoogle()
{
    return Socialite::driver('google')->stateless()->redirect();
}

public function handleGoogleCallback(Request $request)
{
    $user = Socialite::driver('google')->stateless()->user();

    $existingUser = User::where('google_id', $user->getId())->first();

    if ($existingUser) {
        Auth::login($existingUser);
        // $token = $existingUser->createToken('GoogleToken')->accessToken;
        // return response()->json(['token' => $token], 200);
        // return redirect()->intended('/');
        return 'berhasil login';


    } else {
        $level = 'user';
        $imagePath = public_path('user.jpg');
        $uploadedImage = Cloudinary::upload($imagePath);
        
        $newUser = User::create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'google_id' => $user->getId(),
            'level' => $level,
            'image' => $uploadedImage->getSecurePath(),
            'password' => Hash::make(mt_rand(100000, 999999)) 
        ]);

        Auth::login($newUser);
        // $token = $newUser->createToken('GoogleToken')->accessToken;
        // return response()->json(['token' => $token], 200);
        return 'berhasil login';

    }

    
    }

    public function changePassword(Request $request, $id){
        
        $user = User::findOrfail($id);
        $validate = Validator::make([
            'old_password' => $request->old_password,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ], [
            'old_password' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

    if($validate->fails()){
        return response()->json([
            'message' => 'validation Fails',
            'Error  ' =>  $validate->errors()
        ]);
    }

    if(Hash::check($request->old_password, $user->password)){
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message'=> 'Password Successfuly Update'
        ]);
    }
    else{
        return response()->json([
            'message'=> 'Old Password does match'
        ]);
    }
    }  

}
