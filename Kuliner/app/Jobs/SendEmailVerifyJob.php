<?php

namespace App\Jobs;

use App\Mail\SendEmailVerify;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmailVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    public function __construct($user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl= $verificationUrl;
    }

    public function handle(): void
    {
        Mail::to($this->user->email)->send(new SendEmailVerify($this->user, $this->verificationUrl));
    }
}
