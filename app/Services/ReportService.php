<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/**
 * Shared query logic for the admin Reports page (app/Filament/Pages/Reports.php)
 * and its CSV export (app/Http/Controllers/Admin/ReportExportController.php) -
 * one place for each report's numbers so the page and its export can never
 * silently drift apart.
 */
class ReportService
{
    public function __construct(
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
    ) {}

    protected function scopePeriod(Builder|Relation $query, string $column = 'created_at'): Builder|Relation
    {
        if ($this->dateFrom) {
            $query->whereDate($column, '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate($column, '<=', $this->dateTo);
        }

        return $query;
    }

    /**
     * @return array{by_status: Collection, rows: Collection}
     */
    public function partnerReport(): array
    {
        $partners = $this->scopePeriod(Partner::query())->get();

        return [
            'by_status' => $partners->countBy('status'),
            'rows' => $partners->map(fn (Partner $partner) => [
                'name' => $partner->name,
                'status' => $partner->status,
                'leads' => $partner->leads()->count(),
                'customers' => $partner->customers()->count(),
                'commission' => (float) $partner->commissions()->whereIn('status', ['approved', 'paid'])->sum('amount'),
            ]),
        ];
    }

    public function leadReport(): Collection
    {
        return $this->scopePeriod(Lead::query()->with('partner'))
            ->get()
            ->groupBy(fn (Lead $lead) => $lead->partner?->name ?? 'Tanpa Partner')
            ->map(fn (Collection $leads) => $leads->countBy('status'));
    }

    /**
     * @return Collection<int, array{status: string, count: int, total_budget: float}>
     */
    public function projectReport(): Collection
    {
        return $this->scopePeriod(PartnerProject::query())
            ->get()
            ->groupBy('status')
            ->map(fn (Collection $projects, string $status) => [
                'status' => $status,
                'count' => $projects->count(),
                'total_budget' => (float) $projects->sum('budget'),
            ])
            ->values();
    }

    /**
     * @return Collection<int, array{month: string, count: int}>
     */
    public function closingReport(): Collection
    {
        return $this->scopePeriod(Customer::query())
            ->get()
            ->groupBy(fn (Customer $customer) => $customer->created_at->format('Y-m'))
            ->map(fn (Collection $customers, string $month) => [
                'month' => $month,
                'count' => $customers->count(),
            ])
            ->values()
            ->sortBy('month')
            ->values();
    }

    /**
     * @return array{total: float, by_status: Collection, by_partner: Collection}
     */
    public function commissionReport(): array
    {
        $commissions = $this->scopePeriod(Commission::query()->with('partner'))->get();

        return [
            'total' => (float) $commissions->sum('amount'),
            'by_status' => $commissions->groupBy('status')->map(fn (Collection $rows) => (float) $rows->sum('amount')),
            'by_partner' => $commissions->groupBy(fn (Commission $c) => $c->partner?->name ?? 'Tanpa Partner')
                ->map(fn (Collection $rows) => (float) $rows->sum('amount')),
        ];
    }

    /**
     * @return array{total: float, by_status: Collection, by_partner: Collection}
     */
    public function withdrawalReport(): array
    {
        $withdrawals = $this->scopePeriod(Withdrawal::query()->with('partner'))->get();

        return [
            'total' => (float) $withdrawals->sum('amount'),
            'by_status' => $withdrawals->groupBy('status')->map(fn (Collection $rows) => (float) $rows->sum('amount')),
            'by_partner' => $withdrawals->groupBy(fn (Withdrawal $w) => $w->partner?->name ?? 'Tanpa Partner')
                ->map(fn (Collection $rows) => (float) $rows->sum('amount')),
        ];
    }

    /**
     * @return Collection<int, array{name: string, customers: int, commission: float}>
     */
    public function partnerPerformanceReport(): Collection
    {
        return Partner::query()
            ->get()
            ->map(fn (Partner $partner) => [
                'name' => $partner->name,
                'customers' => $this->scopePeriod($partner->customers())->count(),
                'commission' => (float) $this->scopePeriod($partner->commissions()->whereIn('status', ['approved', 'paid']))->sum('amount'),
            ])
            ->sortByDesc('commission')
            ->values();
    }

    public function totalSalesReport(): float
    {
        return (float) $this->scopePeriod(Customer::query())->sum('project_value');
    }
}
