<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TakedownArtikel extends Mailable
{
    use Queueable, SerializesModels;

    public $users;
    public $artikel;

    public function __construct($artikel, $users)
    {
        $this->artikel = $artikel;
        $this->users   = $users;
    }
    
    public function build(){
        return $this->view('Email.TakedownArtikel')
                    ->with([
                        'artikel' => $this->artikel,
                        'users' => $this->users,
                    ]);
    }
}
