<?php

namespace App\Filament\Partner\Widgets;

use App\Models\LeadReminder;
use App\Models\PartnerProject;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PartnerActivityStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $partner = Auth::guard('partner')->user();

        // "Opportunity" = a lead that has progressed past initial contact
        // but hasn't closed either way yet - not an explicit spec
        // definition, this is the most reasonable reading of the term.
        $opportunityCount = $partner->leads()
            ->whereIn('status', ['opportunity', 'proposal', 'negotiation'])
            ->count();

        $todayReminders = LeadReminder::query()
            ->whereHas('lead', fn ($q) => $q->where('partner_id', $partner->id))
            ->whereDate('remind_at', today())
            ->whereNull('completed_at');

        return [
            Stat::make('Total Lead', $partner->leads()->count()),
            Stat::make('Total Opportunity', $opportunityCount),
            Stat::make('Total Customer', $partner->customers()->count()),
            Stat::make('Total Project', PartnerProject::where('partner_id', $partner->id)->count()),
            Stat::make('Project Available', PartnerProject::where('status', 'available')->count())
                ->description('Bisa diklaim siapa saja'),
            Stat::make('Follow Up Hari Ini', (clone $todayReminders)->where('type', 'follow_up')->count()),
            Stat::make('Meeting Hari Ini', (clone $todayReminders)->where('type', 'meeting')->count()),
        ];
    }
}
