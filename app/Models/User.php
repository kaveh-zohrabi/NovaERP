<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'employee_code',
    'avatar',
    'phone',
    'status',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'web';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get all companies this user belongs to.
     *
     * Uses pivot table for future multi-company support.
     * Each pivot row has is_default flag.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /**
     * Get the user's default (current) company.
     *
     * In single-company mode, this is the only company.
     * In multi-company mode, this is the active company.
     */
    public function defaultCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::Active);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', UserStatus::Inactive);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', UserStatus::Suspended);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        return null;
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);

        if (count($parts) >= 2) {
            return strtoupper(mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1));
        }

        return strtoupper(mb_substr($this->name, 0, 2));
    }

    /*
    |--------------------------------------------------------------------------
    | Company Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if user belongs to a specific company.
     */
    public function belongsToCompany(Company $company): bool
    {
        return $this->companies()->where('company_id', $company->id)->exists();
    }

    /**
     * Get the current company from session.
     */
    public function currentCompany(): ?Company
    {
        $companyId = session('company_id');

        if (! $companyId) {
            return $this->companies()->where('is_default', true)->first();
        }

        return Company::find($companyId);
    }

    /**
     * Switch user's active company.
     */
    public function switchCompany(Company $company): bool
    {
        if (! $this->belongsToCompany($company)) {
            return false;
        }

        session(['company_id' => $company->id]);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function isInactive(): bool
    {
        return $this->status === UserStatus::Inactive;
    }

    public function isSuspended(): bool
    {
        return $this->status === UserStatus::Suspended;
    }

    public function canAccess(): bool
    {
        return $this->isActive();
    }

    public function recordLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
