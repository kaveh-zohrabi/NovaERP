<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'company_id',
    'entry_number',
    'date',
    'description',
    'reference_type',
    'reference_id',
    'status',
])]
class JournalEntry extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }

    public function isReversed(): bool
    {
        return $this->status === 'reversed';
    }

    public function getTotalDebit(): float
    {
        return (float) $this->lines()->sum('debit');
    }

    public function getTotalCredit(): float
    {
        return (float) $this->lines()->sum('credit');
    }

    public function isBalanced(): bool
    {
        return abs($this->getTotalDebit() - $this->getTotalCredit()) < 0.01;
    }
}
