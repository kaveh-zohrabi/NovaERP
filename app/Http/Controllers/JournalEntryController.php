<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Services\JournalEntryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    public function __construct(
        private readonly JournalEntryService $journalEntryService,
    ) {}

    public function index(Request $request): View
    {
        $entries = JournalEntry::with('lines.account')
            ->when($request->search, fn ($query, $search) => $query
                ->where('entry_number', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            )
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('accounting.journal-entries.index', compact('entries'));
    }

    public function create(): View
    {
        $accounts = \App\Models\ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        return view('accounting.journal-entries.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'reference_type' => ['nullable', 'string'],
            'reference_id' => ['nullable', 'integer'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:chart_of_accounts,id'],
            'lines.*.debit' => ['required', 'numeric', 'min:0'],
            'lines.*.credit' => ['required', 'numeric', 'min:0'],
            'lines.*.description' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->journalEntryService->create(
                [
                    'company_id' => $validated['company_id'],
                    'date' => $validated['date'],
                    'description' => $validated['description'],
                    'reference_type' => $validated['reference_type'] ?? null,
                    'reference_id' => $validated['reference_id'] ?? null,
                ],
                $validated['lines'],
                $request->user(),
            );

            return redirect()->route('journal-entries.index')->with('success', 'Journal entry created successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load('lines.account');

        return view('accounting.journal-entries.show', ['entry' => $journalEntry]);
    }

    public function post(JournalEntry $journalEntry): RedirectResponse
    {
        $this->journalEntryService->post($journalEntry);

        return back()->with('success', 'Journal entry posted successfully.');
    }

    public function reverse(JournalEntry $journalEntry): RedirectResponse
    {
        $reversal = $this->journalEntryService->reverse($journalEntry);

        return redirect()->route('journal-entries.show', $reversal)->with('success', 'Journal entry reversed successfully.');
    }
}
