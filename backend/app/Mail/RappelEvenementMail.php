<?php

namespace App\Mail;

use App\Models\Inscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RappelEvenementMail extends Mailable
{
    use Queueable;

    public function __construct(
        public Inscription $inscription
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel — ' . $this->inscription->evenement->titre . ' approche',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rappel',
        );
    }
}
