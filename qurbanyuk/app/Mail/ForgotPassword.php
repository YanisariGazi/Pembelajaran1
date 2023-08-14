<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;
    
    public $user;
    public $resetLink;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $resetLink)
    {
        $this->user = $user;
        $this->resetLink = $resetLink;
        // $this->verificationUrl = $verificationUrl;
    }
    public function build()
    {
        return $this->view('VerifyEmail.ForgotPassword')
        ->with([
            'user' => $this->user,
            'resetLink' => $this->resetLink
        ]);       
    }
    /**
     * Get the message envelope.
     */
//     public function content(): Content
// {
//     return new Content(
//         markdown: 'VerifyEmail.FogotPassword',
//         with: [
//             'user' => $this->user,
//             // 'token' => $this->token
//         ],
//     );
// }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
