<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ActivityLog;
use App\Services\AuditService;
use App\Services\ActivityService;
use App\Services\AuditExportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
        private readonly ActivityService $activityService,
        private readonly AuditExportService $exportService,
    ) {}

    public function index(Request $request): View
    {
        $logs = $this->auditService->query(
            $request->user()->company_id ?? 1,
            $request->only(['event', 'user_id', 'auditable_type', 'date_from', 'date_to', 'search']),
        );

        return view('audit.index', compact('logs'));
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user');

        return view('audit.show', ['log' => $auditLog]);
    }

    public function history(Request $request): View
    {
        $activities = $this->activityService->query(
            $request->user()->company_id ?? 1,
            $request->only(['activity_type', 'user_id', 'subject_type', 'date_from', 'date_to', 'search']),
        );

        return view('audit.activity', compact('activities'));
    }

    public function entityHistory(Request $request): View
    {
        $request->validate([
            'auditable_type' => ['required', 'string'],
            'auditable_id' => ['required', 'integer'],
        ]);

        $modelClass = $request->auditable_type;
        $model = $modelClass::findOrFail($request->auditable_id);
        $history = $this->auditService->getHistory($model);

        return view('audit.entity-history', compact('history', 'model'));
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $format = $request->get('format', 'csv');
        $path = $this->exportService->exportAuditLogs(
            $request->user()->company_id ?? 1,
            $request->only(['event', 'user_id', 'date_from', 'date_to']),
            $format,
        );

        $filename = basename($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
}
