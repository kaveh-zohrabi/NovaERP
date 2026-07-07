<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'assigned_to',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name',
        'source',
        'status',
        'lost_reason',
        'estimated_value',
        'converted_at',
        'converted_customer_id',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'converted_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
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

    public function convertedCustomer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'converted_customer_id');
    }

    public function isConverted(): bool
    {
        return $this->converted_at !== null;
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
