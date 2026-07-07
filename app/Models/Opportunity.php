<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'lead_id',
        'pipeline_id',
        'pipeline_stage_id',
        'assigned_to',
        'title',
        'description',
        'expected_value',
        'probability',
        'expected_closing_date',
        'status',
        'lost_reason',
    ];

    protected function casts(): array
    {
        return [
            'expected_value' => 'decimal:2',
            'probability' => 'decimal:2',
            'expected_closing_date' => 'date',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'subjectable_id')
            ->where('subjectable_type', self::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'notable_id')
            ->where('notable_type', self::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'taskable_id')
            ->where('taskable_type', self::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isWon(): bool
    {
        return $this->status === 'won';
    }

    public function isLost(): bool
    {
        return $this->status === 'lost';
    }
}
