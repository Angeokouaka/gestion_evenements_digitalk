<?php

namespace App\Mail;

use App\Models\Certificat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

class CertificatMail extends Mailable
{
    use Queueable;

    public function __construct(
        public Certificat $certificat
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre attestation de participation — ' . $this->certificat->inscription->evenement->titre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificat',
        );
    }

    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromStorageDisk('public', $this->certificat->url_fichier)
                ->as('attestation-participation.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
