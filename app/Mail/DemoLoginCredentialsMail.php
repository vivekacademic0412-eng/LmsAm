<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DemoLoginCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $demoUrl;

    public function __construct($user, $demoUrl)
    {
        $this->user = $user;
        $this->demoUrl = $demoUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Academic Mantra Demo Access Link',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demo-login',
            with: [
                'user' => $this->user,
                'demoUrl' => $this->demoUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}