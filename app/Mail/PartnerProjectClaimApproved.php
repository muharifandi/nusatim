<?php

namespace App\Mail;

use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerProjectClaimApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PartnerProject $project,
        public Partner $partner,
        public SiteSetting $siteSettings,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Klaim Project \"{$this->project->name}\" Disetujui",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.partner-project-claim-approved',
            with: [
                'project' => $this->project,
                'partner' => $this->partner,
                'siteSettings' => $this->siteSettings,
            ],
        );
    }
}
