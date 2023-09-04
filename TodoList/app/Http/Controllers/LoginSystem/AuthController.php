<?php

namespace App\Http\Controllers\LoginSystem;

use App\Models\User;
use App\Mail\SendOtpEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ]);
        }
        $otpCode = rand(1000, 9999);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->otp_code = $otpCode;
        $user->password = Hash::make($request->password);
        $user->save();

        Mail::to($user->email)->send(new SendOtpEmail($user));
        
        return response()->json([
            'Massage' => 'Success Create User',
            'Register' => $user
        ]);
    }

    public function verifyOtp(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp_code' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Fails',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Cari pengguna berdasarkan email yang diberikan
        $user = User::where('email', $request->email)->first();
    
        if ($user && $request->otp_code == $user->otp_code) {
            // Jika kode OTP cocok, tandai email sebagai terverifikasi dan hapus kode OTP
            $user->email_verified_at = now();
            $user->otp_code = null;
            $user->save();
    
            // $token = $user->createToken('token')->accessToken; jika ingin menggunakan ini maka response nya gunakan $token->token
            $token = $user->createToken('token')->plainTextToken;
            return response()->json([
                'message' => 'Success',
                'token' => $token // 
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid email or OTP code',
            ], 422);
        }

    }


    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        // Coba otentikasi sebagai pengguna
        if (Auth::attempt($credentials)) {
            // Otentikasi pengguna berhasil
            $user = Auth::user();
            // $user = auth()->user();

            // Periksa status verifikasi email
            if ($user->email_verified_at) {
                
                $token = $user->createToken('Token')->plainTextToken;

                    return response()->json([
                        'Message' => 'Success',
                        'token' => $token], 200);
                
            } else {
                Auth::logout();
                return response()->json(['message' => 'Email not verified, Please Verify Your Email'], 401);
            }
        }

        // Otentikasi gagal
        return response()->json(['message' => 'Email or password is incorrect.'], 401);
    }
}
