<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Support\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanyService extends BaseService
{
    private const LOGOS_DISK = 'public';

    private const LOGOS_PATH = 'logos';

    /**
     * Create a new company and assign the creator.
     */
    public function create(array $data, User $creator): Company
    {
        return $this->transaction(function () use ($data, $creator) {
            $company = Company::create(
                array_merge(
                    $this->mapData($data),
                    ['created_by' => $creator->id],
                )
            );

            if ($data['logo'] ?? null) {
                $company->update(['logo' => $data['logo']->store(self::LOGOS_PATH, self::LOGOS_DISK)]);
            }

            $company->users()->attach($creator->id, ['is_default' => true]);

            return $company;
        });
    }

    /**
     * Update an existing company.
     */
    public function update(Company $company, array $data): Company
    {
        $mappedData = $this->mapData($data);
        $mappedData['logo'] = $this->handleLogoUpload($data['logo'] ?? null, $company->logo);
        $mappedData['updated_by'] = auth()->id();

        $company->update($mappedData);

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
     * Soft delete a company. Prevents deletion if company has users.
     *
     * @return array{success: bool, message: string}
     */
    public function delete(Company $company): array
    {
        if ($company->hasUsers()) {
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
     * Permanently delete a company and its assets.
     */
    public function forceDelete(Company $company): void
    {
        if ($company->logo) {
            Storage::disk(self::LOGOS_DISK)->delete($company->logo);
        }

        $company->users()->detach();
        $company->forceDelete();
    }

    /**
     * Map validated data to company attributes.
     */
    private function mapData(array $data): array
    {
        return [
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
            'status' => $data['status'] ?? 'active',
            'settings' => $data['settings'] ?? null,
        ];
    }

    /**
     * Handle logo upload and old logo cleanup.
     */
    private function handleLogoUpload(?UploadedFile $newLogo, ?string $oldLogoPath): ?string
    {
        if (! $newLogo) {
            return $oldLogoPath;
        }

        if ($oldLogoPath) {
            Storage::disk(self::LOGOS_DISK)->delete($oldLogoPath);
        }

        return $newLogo->store(self::LOGOS_PATH, self::LOGOS_DISK);
    }
}
