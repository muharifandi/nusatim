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

class PartnerProjectClaimRejected extends Mailable
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
            subject: "Klaim Project \"{$this->project->name}\" Belum Bisa Disetujui",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.partner-project-claim-rejected',
            with: [
                'project' => $this->project,
                'partner' => $this->partner,
                'siteSettings' => $this->siteSettings,
            ],
        );
    }
}
