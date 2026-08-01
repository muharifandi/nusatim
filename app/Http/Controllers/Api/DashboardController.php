<?php

namespace App\Http\Controllers\Api;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadReminder;
use App\Models\Partner;
use App\Models\PartnerProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OA;

/**
 * Mirrors the 4 widgets on the Filament Partner dashboard (PartnerActivityStats,
 * PartnerFinanceStats, PartnerPipelineChart, PartnerClosingChart,
 * PartnerCommissionChart) as a single JSON payload, same underlying queries,
 * so the numbers a mobile app shows always match the web panel exactly.
 */
class DashboardController extends Controller
{
    #[OA\Get(
        path: '/dashboard',
        tags: ['Dashboard'],
        summary: 'Ringkasan aktivitas & keuangan partner',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'activity', properties: [
                    new OA\Property(property: 'total_leads', type: 'integer'),
                    new OA\Property(property: 'total_opportunities', type: 'integer'),
                    new OA\Property(property: 'total_customers', type: 'integer'),
                    new OA\Property(property: 'total_projects', type: 'integer'),
                    new OA\Property(property: 'available_projects', type: 'integer'),
                    new OA\Property(property: 'follow_ups_today', type: 'integer'),
                    new OA\Property(property: 'meetings_today', type: 'integer'),
                ], type: 'object'),
                new OA\Property(property: 'finance', properties: [
                    new OA\Property(property: 'total_project_value', type: 'number'),
                    new OA\Property(property: 'total_commission', type: 'number'),
                    new OA\Property(property: 'pending_commission', type: 'number'),
                    new OA\Property(property: 'available_balance', type: 'number'),
                    new OA\Property(property: 'total_withdrawn', type: 'number'),
                    new OA\Property(property: 'sales_target', properties: [
                        new OA\Property(property: 'target_amount', type: 'number', nullable: true),
                        new OA\Property(property: 'achieved_amount', type: 'number'),
                        new OA\Property(property: 'achieved_percentage', type: 'number', nullable: true),
                    ], type: 'object', nullable: true),
                ], type: 'object'),
                new OA\Property(property: 'pipeline', type: 'object', description: 'Jumlah lead per status', additionalProperties: new OA\AdditionalProperties(type: 'integer')),
                new OA\Property(property: 'closing_trend', properties: [
                    new OA\Property(property: 'labels', type: 'array', items: new OA\Items(type: 'string')),
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'integer')),
                ], type: 'object', description: '12 bulan terakhir, jumlah Customer baru per bulan'),
                new OA\Property(property: 'commission_trend', properties: [
                    new OA\Property(property: 'labels', type: 'array', items: new OA\Items(type: 'string')),
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'number')),
                ], type: 'object', description: '12 bulan terakhir, total nominal komisi per bulan'),
            ])),
        ]
    )]
    public function summary(Request $request): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();

        return response()->json([
            'activity' => $this->activity($partner),
            'finance' => $this->finance($partner),
            'pipeline' => $this->pipeline($partner),
            'closing_trend' => $this->monthlyTrend(
                Customer::query()->where('partner_id', $partner->id),
                fn ($rows) => $rows->count(),
            ),
            'commission_trend' => $this->monthlyTrend(
                Commission::query()->where('partner_id', $partner->id),
                fn ($rows) => (float) $rows->sum('amount'),
            ),
        ]);
    }

    /**
     * @return array<string, int>
     */
    protected function activity(Partner $partner): array
    {
        $opportunityCount = $partner->leads()
            ->whereIn('status', ['opportunity', 'proposal', 'negotiation'])
            ->count();

        $todayReminders = LeadReminder::query()
            ->whereHas('lead', fn ($q) => $q->where('partner_id', $partner->id))
            ->whereDate('remind_at', today())
            ->whereNull('completed_at');

        return [
            'total_leads' => $partner->leads()->count(),
            'total_opportunities' => $opportunityCount,
            'total_customers' => $partner->customers()->count(),
            'total_projects' => PartnerProject::where('partner_id', $partner->id)->count(),
            'available_projects' => PartnerProject::where('status', 'available')->count(),
            'follow_ups_today' => (clone $todayReminders)->where('type', 'follow_up')->count(),
            'meetings_today' => (clone $todayReminders)->where('type', 'meeting')->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function finance(Partner $partner): array
    {
        $totalProjectValue = (float) $partner->customers()->sum('project_value');
        $target = $partner->currentSalesTarget();

        return [
            'total_project_value' => $totalProjectValue,
            'total_commission' => (float) $partner->commissions()->sum('amount'),
            'pending_commission' => (float) $partner->commissions()->where('status', 'pending')->sum('amount'),
            'available_balance' => $partner->availableBalance(),
            'total_withdrawn' => (float) $partner->withdrawals()->where('status', 'paid')->sum('amount'),
            'sales_target' => $target ? [
                'target_amount' => (float) $target->target_amount,
                'achieved_amount' => $totalProjectValue,
                'achieved_percentage' => $target->target_amount > 0
                    ? round(($totalProjectValue / (float) $target->target_amount) * 100, 1)
                    : 0,
            ] : null,
        ];
    }

    /**
     * @return array<string, int>
     */
    protected function pipeline(Partner $partner): array
    {
        $statuses = ['new', 'contacted', 'qualified', 'opportunity', 'proposal', 'negotiation', 'won', 'lost'];

        $counts = Lead::query()
            ->where('partner_id', $partner->id)
            ->get()
            ->countBy('status');

        return collect($statuses)->mapWithKeys(fn (string $status) => [$status => $counts->get($status, 0)])->all();
    }

    /**
     * Trailing 12 months, keyed by "M Y" label - same startOfMonth()-then-
     * subMonths() ordering as PartnerClosingChart/PartnerCommissionChart to
     * avoid the Carbon month-overflow bug near month-end dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return array{labels: array<int, string>, data: array<int, int|float>}
     */
    protected function monthlyTrend($query, \Closure $aggregate): array
    {
        $months = collect(range(11, 0))->map(fn ($i) => Carbon::now()->startOfMonth()->subMonths($i));

        $rows = $query->where('created_at', '>=', $months->first())->get()
            ->groupBy(fn ($model) => $model->created_at->format('Y-m'));

        return [
            'labels' => $months->map(fn (Carbon $m) => $m->translatedFormat('M Y'))->all(),
            'data' => $months->map(function (Carbon $m) use ($rows, $aggregate) {
                $group = $rows->get($m->format('Y-m'));

                return $group ? $aggregate($group) : 0;
            })->all(),
        ];
    }
}
