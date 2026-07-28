<?php

namespace App\Filament\Partner\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;

/**
 * Landing page for any logged-in partner whose registration isn't approved
 * yet - EnsurePartnerApproved redirects everything else here. Shows Pending
 * Review or the rejection reason instead of a real dashboard.
 */
class PartnerStatus extends Page
{
    protected static ?string $slug = 'status';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.partner.pages.partner-status';

    public function getTitle(): string
    {
        return 'Status Pendaftaran';
    }

    public function partner()
    {
        return Filament::auth()->user();
    }
}
