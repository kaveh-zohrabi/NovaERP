<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Support\BaseService;
use Illuminate\Support\Facades\Storage;

class CompanyService extends BaseService
{
    /**
     * Create a new company and assign the creator.
     *
     * @param  array<string, mixed>  $data  Validated company data
     * @param  User  $creator  The user creating the company
     */
    public function create(array $data, User $creator): Company
    {
        return $this->transaction(function () use ($data, $creator) {
            $logoPath = null;
            if ($data['logo'] ?? null) {
                $logoPath = $data['logo']->store('logos', 'public');
            }

            $company = Company::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'legal_name' => $data['legal_name'] ?? null,
                'registration_number' => $data['registration_number'] ?? null,
                'tax_number' => $data['tax_number'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'website' => $data['website'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'logo' => $logoPath,
                'status' => $data['status'] ?? 'active',
                'settings' => $data['settings'] ?? null,
                'created_by' => $creator->id,
            ]);

            $company->users()->attach($creator->id, ['is_default' => true]);

            return $company;
        });
    }

    /**
     * Update an existing company.
     *
     * @param  Company  $company  The company to update
     * @param  array<string, mixed>  $data  Validated company data
     */
    public function update(Company $company, array $data): Company
    {
        $logoPath = $company->logo;
        if ($data['logo'] ?? null) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $logoPath = $data['logo']->store('logos', 'public');
        }

        $company->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'legal_name' => $data['legal_name'] ?? null,
            'registration_number' => $data['registration_number'] ?? null,
            'tax_number' => $data['tax_number'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'website' => $data['website'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'logo' => $logoPath,
            'status' => $data['status'],
            'settings' => $data['settings'] ?? null,
            'updated_by' => auth()->id(),
        ]);

        return $company->fresh();
    }

    /**
     * Activate a company.
     */
    public function activate(Company $company): bool
    {
        if ($company->isActive()) {
            return false;
        }

        $company->update(['status' => 'active']);

        return true;
    }

    /**
     * Deactivate a company.
     */
    public function deactivate(Company $company): bool
    {
        if (! $company->isActive()) {
            return false;
        }

        $company->update(['status' => 'inactive']);

        return true;
    }

    /**
     * Soft delete a company.
     *
     * Prevents deletion if company has active users.
     *
     * @return array{success: bool, message: string}
     */
    public function delete(Company $company): array
    {
        if ($company->users()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete company with active users. Remove all users first.',
            ];
        }

        $company->delete();

        return [
            'success' => true,
            'message' => 'Company deleted successfully.',
        ];
    }

    /**
     * Restore a soft-deleted company.
     */
    public function restore(Company $company): bool
    {
        if (! $company->trashed()) {
            return false;
        }

        $company->restore();

        return true;
    }

    /**
     * Permanently delete a company.
     *
     * Warning: This cannot be undone.
     */
    public function forceDelete(Company $company): void
    {
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->users()->detach();
        $company->forceDelete();
    }
}
