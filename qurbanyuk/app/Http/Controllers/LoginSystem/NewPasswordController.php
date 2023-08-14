<?php

namespace App\Http\Controllers\LoginSystem;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as RulesPassword;

class NewPasswordController extends Controller
{
    
        public function sendResetLink(Request $request)
        {
            $request->validate([
                'email' => 'required|email',
                ]);
                
                $user = User::where('email', $request->email)->first();
                
                if (!$user || !$user->email_verified_at) {
                    return response()->json(['message' => 'Email not found Or Not Verify'], 404);
                }else{
                    $token = Password::getRepository()->create($user);
                    // Kirim email dengan link reset password ke pengguna
                    $resetLink = 'https://example.com/reset-password?token=' . $token;

                    Mail::to($request->email)->send(new ForgotPassword($user, $resetLink));
                    
                    return response()->json(['message' => 'Reset password link sent successfully']);
                    
                }
                
                
            
        }

        public function resetPassword(Request $request)
        {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            $response = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new \Illuminate\Auth\Events\PasswordReset($user));
                }
            );

            if ($response === Password::PASSWORD_RESET) {
                return response()->json(['message' => 'Password reset successfully']);
            } else {
                return response()->json(['message' => 'Failed to reset password'], 400);
            }
        }
}
