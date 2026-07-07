<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ReportDefinition;
use App\Services\ExportService;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly ExportService $exportService,
    ) {}

    public function index(Request $request): View
    {
        $availableReports = $this->reportService->getAvailableReports($request->user()->company_id ?? 1);

        return view('reporting.reports.index', ['reports' => $availableReports]);
    }

    public function show(string $type, Request $request): View
    {
        $data = $this->reportService->executeReport($type, array_merge(
            ['company_id' => $request->user()->company_id ?? 1],
            $request->only(['start_date', 'end_date', 'customer_id', 'product_id']),
        ));

        return view('reporting.reports.show', ['type' => $type, 'data' => $data]);
    }

    public function export(string $type, Request $request): Response|BinaryFileResponse
    {
        $data = $this->reportService->executeReport($type, array_merge(
            ['company_id' => $request->user()->company_id ?? 1],
            $request->only(['start_date', 'end_date']),
        ));

        $records = $data['accounts'] ?? $data['products'] ?? $data['items'] ?? $data['customers'] ?? $data['movements'] ?? $data['daily_sales'] ?? [];
        $filename = $type.'-'.now()->format('Y-m-d-His');

        $format = $request->get('format', 'csv');

        if ($format === 'pdf') {
            $path = $this->exportService->exportPdf($records, $filename);
        } else {
            $path = $this->exportService->exportCsv($records, $filename);
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
