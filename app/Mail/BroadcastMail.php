<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public string $body,
        public ?string $senderName = null,
        public array $mailAttachments = []
    ) {
    }


    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->title);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.broadcast',
            with: [
                'title' => $this->title,
                'body' => $this->body,
                'senderName' => $this->senderName,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return collect($this->attachments)->map(function (array $file) {
            $attachment = Attachment::fromPath($file['path'])->as($file['name']);

            if (! empty($file['mime'])) {
                $attachment = $attachment->withMime($file['mime']);
            }

            return $attachment;
        })->all();
    }
}