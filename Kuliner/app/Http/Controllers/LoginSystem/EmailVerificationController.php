<?php

namespace App\Http\Controllers\LoginSystem;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\SendEmailVerify;
use App\Jobs\SendEmailVerifyJob;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
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

    public function resendVerificationEmail(Request $request)
    {
        
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
            
                $SendEmailVerifyJob = new SendEmailVerifyJob($user, $verificationUrl);
                dispatch($SendEmailVerifyJob);

                return response()->json([
                    'message' => 'Resend Verification email, Please check your email.'
                ]);
            }
        }
    }
}
