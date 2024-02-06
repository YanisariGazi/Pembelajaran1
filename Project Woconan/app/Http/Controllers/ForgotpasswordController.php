<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerificationReset;

class ForgotpasswordController extends Controller
{
    // public function reset(Request $request)
    // {
    //     $email = $request->input('email');

    //     if (empty($email)) {
    //         return response()->json(['error' => 'Invalid email'], 400);
    //     }

    //     $token = Password::getRepository()->createNewToken();

    //     // Reset kata sandi dan hapus token lama
    //     Password::broker()->reset(
    //         ['email' => $email, 'token' => $token],
    //         function ($user, $password) {
    //             $user->password = bcrypt($password);
    //             $user->setRememberToken(Str::random(60));
    //             $user->save();
    //         }
    //     );

    //     return response()->json([
    //         'message' => 'Password reset successfully',
    //     ]);
    // }




    // ini reset untuk memperbarui password
//     public function reset(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'password' => 'required|min:8',
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['error' => $validator->errors()], 400);
//     }

//     $email = $request->input('email') ?: Auth::user()->email;

//     $password = $request->input('password');

//     $user = User::where('email', $email)->first();

//     if (!$user) {
//         return response()->json(['error' => 'User not found'], 404);
//     }

//     // Periksa apakah password lama sudah dihapus
//     if ($user->password) {
//         return response()->json(['error' => 'Please reset your password first'], 400);
//     }

//     $user->password = Hash::make($password);
//     $user->save();

//     return response()->json(['message' => 'Password reset successfully'], 200);
// }





// OTP RESET atau LUPA KATA SANDI
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $email = $request->input('email');

        // Cek apakah email pengguna ada dalam database
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Generate OTP
        $otp = mt_rand(100000, 999999);

        // Simpan OTP ke pengguna
        $user->otp_code = $otp;
        $user->otp_expired = now()->addMinutes(10); // Atur waktu kadaluarsa OTP sesuai kebutuhan Anda
        $user->save();

        // Kirim email verifikasi OTP
        Mail::to($user->email)->send(new VerificationReset($otp, $user));

        return response()->json(['message' => 'OTP has been sent to your email']);
    }


    public function verifikasi_reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'otp' => 'required|string|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user && $user->otp_code == $request->input('otp')) {
            if ($user->otp_expired && now()->lt($user->otp_expired)) {
                $user->email_verified_at = now();
                $user->save();

                return response()->json(['message' => 'Verifikasi email berhasil, Masuk Sirsak jan diluar']);
            } else {
                return response()->json(['message' => 'Kode OTP sudah kedaluwarsa']);
            }
        } else {
            return response()->json(['message' => 'Kode OTP tidak valid']);
        }
    }


    public function showResetForm(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (empty($email) || empty($password)) {
            return response()->json(['error' => 'Invalid email or password'], 400);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->password = Hash::make($password);
        $user->save();

        return response()->json(['message' => 'Password has been reset successfully'], 200);
    }




}
