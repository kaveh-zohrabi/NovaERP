<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_definition_id',
        'executed_by',
        'filters',
        'status',
        'generated_file',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
        ];
    }

    public function reportDefinition(): BelongsTo
    {
        return $this->belongsTo(ReportDefinition::class);
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
