<?php

namespace App\Mail;

use App\Models\Partner;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerRegistrationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Partner $partner,
        public SiteSetting $siteSettings,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pendaftaran Partner {$this->siteSettings->company_name} Sedang Ditinjau",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.partner-registration-received',
            with: [
                'partner' => $this->partner,
                'siteSettings' => $this->siteSettings,
            ],
        );
    }
}
