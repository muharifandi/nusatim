<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV export for the admin Reports page (app/Filament/Pages/Reports.php) -
 * a plain route/controller rather than a Livewire action, matching how
 * every other file download in this app works (lead/withdrawal/marketing
 * material documents).
 */
class ReportExportController extends Controller
{
    public function export(Request $request, string $report): StreamedResponse
    {
        $service = new ReportService($request->query('date_from'), $request->query('date_to'));

        $rows = match ($report) {
            'partner' => $this->partnerRows($service),
            'lead' => $this->leadRows($service),
            'project' => $this->projectRows($service),
            'closing' => $this->closingRows($service),
            'commission' => $this->commissionRows($service),
            'withdrawal' => $this->withdrawalRows($service),
            'performance' => $this->performanceRows($service),
            default => abort(404),
        };

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, "report-{$report}.csv", ['Content-Type' => 'text/csv']);
    }

    protected function partnerRows(ReportService $service): array
    {
        $rows = [['Nama', 'Status', 'Lead', 'Customer', 'Komisi']];

        foreach ($service->partnerReport()['rows'] as $row) {
            $rows[] = [$row['name'], $row['status'], $row['leads'], $row['customers'], $row['commission']];
        }

        return $rows;
    }

    protected function leadRows(ReportService $service): array
    {
        $rows = [['Partner', 'Status', 'Jumlah']];

        foreach ($service->leadReport() as $partnerName => $statuses) {
            foreach ($statuses as $status => $count) {
                $rows[] = [$partnerName, $status, $count];
            }
        }

        return $rows;
    }

    protected function projectRows(ReportService $service): array
    {
        $rows = [['Status', 'Jumlah', 'Total Budget']];

        foreach ($service->projectReport() as $row) {
            $rows[] = [$row['status'], $row['count'], $row['total_budget']];
        }

        return $rows;
    }

    protected function closingRows(ReportService $service): array
    {
        $rows = [['Bulan', 'Jumlah Closing']];

        foreach ($service->closingReport() as $row) {
            $rows[] = [$row['month'], $row['count']];
        }

        return $rows;
    }

    protected function commissionRows(ReportService $service): array
    {
        $report = $service->commissionReport();
        $rows = [['Total', $report['total']], [], ['Status', 'Nominal']];

        foreach ($report['by_status'] as $status => $amount) {
            $rows[] = [$status, $amount];
        }

        $rows[] = [];
        $rows[] = ['Partner', 'Nominal'];

        foreach ($report['by_partner'] as $name => $amount) {
            $rows[] = [$name, $amount];
        }

        return $rows;
    }

    protected function withdrawalRows(ReportService $service): array
    {
        $report = $service->withdrawalReport();
        $rows = [['Total', $report['total']], [], ['Status', 'Nominal']];

        foreach ($report['by_status'] as $status => $amount) {
            $rows[] = [$status, $amount];
        }

        $rows[] = [];
        $rows[] = ['Partner', 'Nominal'];

        foreach ($report['by_partner'] as $name => $amount) {
            $rows[] = [$name, $amount];
        }

        return $rows;
    }

    protected function performanceRows(ReportService $service): array
    {
        $rows = [['Partner', 'Customer', 'Komisi']];

        foreach ($service->partnerPerformanceReport() as $row) {
            $rows[] = [$row['name'], $row['customers'], $row['commission']];
        }

        return $rows;
    }
}
