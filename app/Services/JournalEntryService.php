<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\User;
use App\Support\BaseService;

class JournalEntryService extends BaseService
{
    public function create(array $data, array $lines, User $creator): JournalEntry
    {
        $totalDebit = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (abs($totalDebit - $totalCredit) >= 0.01) {
            throw new \InvalidArgumentException(
                'Journal entry must be balanced. Debit: '.$totalDebit.', Credit: '.$totalCredit
            );
        }

        if (count($lines) < 2) {
            throw new \InvalidArgumentException('Journal entry must have at least 2 lines.');
        }

        return $this->transaction(function () use ($data, $lines, $creator) {
            $entry = JournalEntry::create([
                'company_id' => $data['company_id'],
                'entry_number' => $this->generateEntryNumber($data['company_id']),
                'date' => $data['date'],
                'description' => $data['description'],
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'status' => 'draft',
                'created_by' => $creator->id,
            ]);

            foreach ($lines as $line) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                ]);
            }

            return $entry;
        });
    }

    public function post(JournalEntry $entry): bool
    {
        if (! $entry->isDraft()) {
            throw new \InvalidArgumentException('Only draft entries can be posted.');
        }

        if (! $entry->isBalanced()) {
            throw new \InvalidArgumentException('Journal entry must be balanced before posting.');
        }

        if ($entry->lines()->count() < 2) {
            throw new \InvalidArgumentException('Journal entry must have at least 2 lines.');
        }

        $entry->update(['status' => 'posted']);

        return true;
    }

    public function reverse(JournalEntry $entry): JournalEntry
    {
        if (! $entry->isPosted()) {
            throw new \InvalidArgumentException('Only posted entries can be reversed.');
        }

        $reversalLines = $entry->lines->map(fn ($line) => [
            'account_id' => $line->account_id,
            'debit' => $line->credit,
            'credit' => $line->debit,
            'description' => 'Reversal: '.$line->description,
        ])->toArray();

        $reversal = $this->create([
            'company_id' => $entry->company_id,
            'date' => now()->toDateString(),
            'description' => 'Reversal of '.$entry->entry_number,
            'reference_type' => JournalEntry::class,
            'reference_id' => $entry->id,
        ], $reversalLines, auth()->user());

        $entry->update(['status' => 'reversed']);

        return $reversal;
    }

    public function generateEntryNumber(int $companyId): string
    {
        $year = now()->format('Y');
        $sequence = JournalEntry::where('company_id', $companyId)
            ->whereYear('date', now()->year)
            ->count() + 1;

        return "JE-{$companyId}-{$year}-".str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }
}
