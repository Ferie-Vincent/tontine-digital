<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TontineNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageContent;
    public ?string $actionUrl;
    public string $actionLabel;

    public function __construct(
        string $title,
        string $messageContent,
        ?string $actionUrl = null,
        string $actionLabel = 'Voir la tontine',
    ) {
        $this->subject = $title;
        $this->messageContent = $messageContent;
        $this->actionUrl = $actionUrl;
        $this->actionLabel = $actionLabel;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject . ' - DIGI-TONTINE',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tontine-notification',
            with: [
                'messageContent' => $this->messageContent,
                'actionUrl' => $this->actionUrl,
                'actionLabel' => $this->actionLabel,
            ],
        );
    }
}
