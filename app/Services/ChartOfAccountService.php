<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\User;
use App\Support\BaseService;

class ChartOfAccountService extends BaseService
{
    public function create(array $data, User $creator): ChartOfAccount
    {
        return ChartOfAccount::create(
            array_merge($data, ['created_by' => $creator->id])
        );
    }

    public function update(ChartOfAccount $account, array $data): ChartOfAccount
    {
        $account->update($data);

        return $account->fresh();
    }

    public function delete(ChartOfAccount $account): array
    {
        $account->delete();

        return [
            'success' => true,
            'message' => 'Account deleted successfully.',
        ];
    }

    public function restore(ChartOfAccount $account): bool
    {
        if (! $account->trashed()) {
            return false;
        }

        $account->restore();

        return true;
    }
}
