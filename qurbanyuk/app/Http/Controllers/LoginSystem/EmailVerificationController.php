<?php

namespace App\Http\Controllers\LoginSystem;

use App\Models\User;
use App\Mail\VerifyE;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class EmailVerificationController extends Controller
{
    public function resendVerificationEmail(Request $request)
{
    // $email = $request->input('email');
    $request->validate([
        'email' => 'required|email'
    ]);
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found.'
        ], 404);
    }else{
        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified.'
            ], 422);
        }else{
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
            );
        
            Mail::to($user->email)->send(new VerifyE($user, $verificationUrl));
        
            return response()->json([
                'message' => 'Verification email sent, Please check your email.'
            ]);
        }
    }
}

    public function verify(Request $request, $id)
    {
        
        if(!$request->hasValidSignature()){
            return [
                'message' => 'Email verified fails'
            ];
        }
        
        $user = User::findOrfail($id);
        

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully'
        ]);
    }
}
