<?php

namespace App\Mail;

use App\Models\Partner;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerPasswordResetRequested extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Partner $partner,
        public string $token,
        public SiteSetting $siteSettings,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reset Password Akun Partner {$this->siteSettings->company_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.partner-password-reset-requested',
            with: [
                'partner' => $this->partner,
                'token' => $this->token,
                'siteSettings' => $this->siteSettings,
                'expiresInMinutes' => (int) config('auth.passwords.partners.expire', 60),
            ],
        );
    }
}
