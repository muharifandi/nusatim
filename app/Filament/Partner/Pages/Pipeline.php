<?php

namespace App\Filament\Partner\Pages;

use App\Models\Lead;
use App\Models\Service;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class Pipeline extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationLabel = 'Sales Pipeline';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.partner.pages.pipeline';

    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'opportunity' => 'Opportunity',
        'proposal' => 'Proposal',
        'negotiation' => 'Negotiation',
        'won' => 'Won',
        'lost' => 'Lost',
    ];

    public ?int $serviceFilter = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function services(): Collection
    {
        return Service::active()->pluck('title', 'id');
    }

    /**
     * @return array<string, Collection<int, Lead>>
     */
    public function getLeadsByStatus(): array
    {
        $leads = Lead::query()
            ->where('partner_id', Auth::guard('partner')->id())
            ->when($this->serviceFilter, fn ($q) => $q->where('service_id', $this->serviceFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('status');

        return collect(self::STATUSES)
            ->keys()
            ->mapWithKeys(fn (string $status) => [$status => $leads->get($status, collect())])
            ->all();
    }

    /**
     * Called from the drag-and-drop board (see pipeline-loader render hook).
     * Re-scopes to the logged-in partner server-side - a partner could
     * otherwise call this Livewire method directly with any lead ID.
     */
    public function moveLead(int $leadId, string $newStatus): void
    {
        if (! array_key_exists($newStatus, self::STATUSES)) {
            abort(422);
        }

        $lead = Lead::where('partner_id', Auth::guard('partner')->id())->findOrFail($leadId);

        $lead->update(['status' => $newStatus]);
    }
}
