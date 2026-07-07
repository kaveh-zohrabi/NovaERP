<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use App\Support\BaseService;

class FinancialReportService extends BaseService
{
    public function getProfitAndLoss(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;
        $startDate = $filters['start_date'] ?? now()->startOfYear()->toDateString();
        $endDate = $filters['end_date'] ?? now()->endOfYear()->toDateString();

        $revenueAccounts = ChartOfAccount::where('company_id', $companyId)
            ->where('type', 'revenue')
            ->get();

        $expenseAccounts = ChartOfAccount::where('company_id', $companyId)
            ->where('type', 'expense')
            ->get();

        $revenueLines = JournalEntryLine::whereHas('journalEntry', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
                ->where('status', 'posted')
                ->whereBetween('date', [$startDate, $endDate]);
        })->whereIn('account_id', $revenueAccounts->pluck('id'))->get();

        $expenseLines = JournalEntryLine::whereHas('journalEntry', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
                ->where('status', 'posted')
                ->whereBetween('date', [$startDate, $endDate]);
        })->whereIn('account_id', $expenseAccounts->pluck('id'))->get();

        $totalRevenue = (float) $revenueLines->sum('credit') - (float) $revenueLines->sum('debit');
        $totalExpenses = (float) $expenseLines->sum('debit') - (float) $expenseLines->sum('credit');

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'revenue' => [
                'total' => $totalRevenue,
                'accounts' => $revenueAccounts->map(fn ($account) => [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => (float) $account->journalEntryLines()
                        ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')
                            ->whereBetween('date', [$startDate, $endDate]))
                        ->sum('credit') - (float) $account->journalEntryLines()
                        ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')
                            ->whereBetween('date', [$startDate, $endDate]))
                        ->sum('debit'),
                ])->toArray(),
            ],
            'expenses' => [
                'total' => $totalExpenses,
                'accounts' => $expenseAccounts->map(fn ($account) => [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => (float) $account->journalEntryLines()
                        ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')
                            ->whereBetween('date', [$startDate, $endDate]))
                        ->sum('debit') - (float) $account->journalEntryLines()
                        ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')
                            ->whereBetween('date', [$startDate, $endDate]))
                        ->sum('credit'),
                ])->toArray(),
            ],
            'net_income' => $totalRevenue - $totalExpenses,
        ];
    }

    public function getBalanceSheet(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;
        $asOfDate = $filters['end_date'] ?? now()->toDateString();

        $accountTypes = ['asset', 'liability', 'equity'];
        $results = [];

        foreach ($accountTypes as $type) {
            $accounts = ChartOfAccount::where('company_id', $companyId)
                ->where('type', $type)
                ->get();

            $results[$type] = [
                'total' => 0,
                'accounts' => $accounts->map(function ($account) use ($type, $asOfDate) {
                    $debitTotal = (float) $account->journalEntryLines()
                        ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')->where('date', '<=', $asOfDate))
                        ->sum('debit');
                    $creditTotal = (float) $account->journalEntryLines()
                        ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')->where('date', '<=', $asOfDate))
                        ->sum('credit');

                    $balance = $type === 'liability' || $type === 'equity'
                        ? $creditTotal - $debitTotal
                        : $debitTotal - $creditTotal;

                    return [
                        'code' => $account->code,
                        'name' => $account->name,
                        'balance' => $balance,
                    ];
                })->toArray(),
            ];

            $results[$type]['total'] = collect($results[$type]['accounts'])->sum('balance');
        }

        $results['total_assets'] = $results['asset']['total'];
        $results['total_liabilities'] = $results['liability']['total'];
        $results['total_equity'] = $results['equity']['total'];

        return $results;
    }

    public function getTrialBalance(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;
        $asOfDate = $filters['end_date'] ?? now()->toDateString();

        $accounts = ChartOfAccount::where('company_id', $companyId)
            ->orderBy('code')
            ->get();

        $rows = $accounts->map(function ($account) use ($asOfDate) {
            $debitTotal = (float) $account->journalEntryLines()
                ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')->where('date', '<=', $asOfDate))
                ->sum('debit');
            $creditTotal = (float) $account->journalEntryLines()
                ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')->where('date', '<=', $asOfDate))
                ->sum('credit');

            return [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'debit' => $debitTotal,
                'credit' => $creditTotal,
            ];
        })->filter(fn ($row) => $row['debit'] > 0 || $row['credit'] > 0)->values()->toArray();

        return [
            'as_of_date' => $asOfDate,
            'accounts' => $rows,
            'total_debit' => collect($rows)->sum('debit'),
            'total_credit' => collect($rows)->sum('credit'),
        ];
    }
}
