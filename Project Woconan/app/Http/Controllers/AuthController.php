<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\VerificationEmail;
use App\Models\ProfilUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class AuthController extends Controller
{


// REGISTER
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'no_hp' => 'required|string|unique:users',
            'pekerjaan' => 'required|string',
            'alamat_lengkap' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $email = $request->email;

        // Pengecekan email harus menggunakan domain @gmail atau @yahoo
        $allowedDomains = ['gmail.com', 'yahoo.com'];
        $domain = substr(strrchr($email, "@"), 1);
        if (!in_array($domain, $allowedDomains)) {
            return response()->json(['error' => 'Email must use @gmail or @yahoo domain']);
        }

        // Pengecekan keberadaan email
        $emailExists = User::where('email', $email)->exists();
        if ($emailExists) {
            return response()->json(['error' => 'Email already exists']);
        }

        // Lakukan tindakan selanjutnya untuk pendaftaran pengguna di sini

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => bcrypt($request->password),
            'no_hp' => $request->no_hp,
            'pekerjaan' => $request->pekerjaan,
            'alamat_lengkap' => $request->alamat_lengkap
        ]);

        // Kirim kode OTP ke email pengguna
        $otp = mt_rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expired = now()->addMinutes(3);
        $user->save();

        $email = new VerificationEmail($otp, $user);
        Mail::to($user->email)->send($email);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
    }

// REGISTER OTOMATIS POTO PROFIL KIRIM KE CLOUDINARY
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8',
    //         'no_hp' => 'required|string|unique:users',
    //         'pekerjaan' => 'required|string',
    //         'alamat_lengkap' => 'required|string'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors());
    //     }

    //     $email = $request->email;

    //     // Pengecekan email harus menggunakan domain @gmail atau @yahoo
    //     $allowedDomains = ['gmail.com', 'yahoo.com'];
    //     $domain = substr(strrchr($email, "@"), 1);
    //     if (!in_array($domain, $allowedDomains)) {
    //         return response()->json(['error' => 'Email must use @gmail or @yahoo domain']);
    //     }

    //     // Pengecekan keberadaan email
    //     $emailExists = User::where('email', $email)->exists();
    //     if ($emailExists) {
    //         return response()->json(['error' => 'Email already exists']);
    //     }

    //     $imagePath = public_path('user.jpg');
    //     // Upload gambar ke Cloudinaryhttps://c8b2-2001-448a-4041-8a63-ba4-8db4-9e99-1343.ngrok-free.app
    //     $uploadedImage = Cloudinary::upload($imagePath);

    //     // Lakukan tindakan selanjutnya untuk pendaftaran pengguna di sini

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $email,
    //         'password' => bcrypt($request->password),
    //         'no_hp' => $request->no_hp,
    //         'pekerjaan' => $request->pekerjaan,
    //         'alamat_lengkap' => $request->alamat_lengkap
    //     ]);

    //     // Simpan data gambar ke dalam tabel profil_user
        // $profilUser = ProfilUser::create([
        //     'user_id' => $user->id,
        //     'gambar' => $uploadedImage->getSecurePath(), // Gunakan URL aman dari Cloudinary
        //     // Isi dengan data profil_user yang lain sesuai kebutuhan
        // ]);

    //     // Kirim kode OTP ke email pengguna
    //     $otp = mt_rand(100000, 999999);
    //     $user->otp_code = $otp;
    //     $user->otp_expired = now()->addMinutes(3);
    //     $user->save();

    //     $email = new VerificationEmail($otp, $user);
    //     Mail::to($user->email)->send($email);

    //     $token = $user->createToken('auth_token')->plainTextToken;


    //     return response()
    //         ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
    // }




    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (!Auth::attempt($credentials)) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     $user_verification = User::where('email', $request->email)->whereNull('email_verified_at')->first();

    //     if ($user_verification) {
    //         return response()->json(['message' => 'Email ini belum diverifikasi'], 401);
    //     }

    //     $user = User::where('email', $request->email)->firstOrFail();

    //     if (!$user->hasVerifiedEmail()) {
    //         return response()->json(['message' => 'Email Anda belum terverifikasi'], 403);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Hi ' . $user->role . ', welcome to beranda',
    //         'access_token' => $token,
    //         'token_type' => 'Bearer',
    //     ]);
    // }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        if ($user->role === 'admin') {
            $token = $user->createToken('admin_token')->plainTextToken;
            $message = 'admin login successful';
        } elseif ($user->role === 'super_admin') {
            $token = $user->createToken('super_admin_token')->plainTextToken;
            $message = 'super_admin login successful';
        } else {
            $token = $user->createToken('user_token')->plainTextToken;
            $message = 'user login successful';
        }


        return response()->json([
            'message' => $message,
            'user_id' => $user->id, // Tambahkan ID pengguna di sini
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }




    public function verifikasi_user(Request $request)
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


    public function kirim_ulang_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            // Check if the current user is the owner of the email
            if ($user->email !== $request->input('email')) {
                return response()->json(['message' => 'Anda tidak memiliki izin untuk menggunakan fitur ini']);
            }

            // Generate new OTP code
            $otp = $this->generateOTP();
            $user->otp_code = $otp;
            $user->otp_expired = now()->addMinutes(5); // Set OTP expiry time

            // Send email with new OTP code
            $data = [
                'otp' => $otp,
                'user' => $user,
            ];

            Mail::to($user->email)->send(new VerificationEmail($otp, $user));

            $user->save();
            return response()->json(['message' => 'OTP baru telah dikirim. Periksa email Anda.']);
        } else {
            return response()->json(['message' => 'User tidak ditemukan']);
        }
    }


      protected function generateOTP()
    {
        return strval(mt_rand(100000, 999999));
    }


    public function logout()
    {
        auth('sanctum')->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }

}
