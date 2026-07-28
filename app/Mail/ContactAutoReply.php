<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
        public SiteSetting $siteSettings,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Terima kasih telah menghubungi {$this->siteSettings->company_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-auto-reply',
            with: [
                'contactMessage' => $this->contactMessage,
                'siteSettings' => $this->siteSettings,
            ],
        );
    }
}
