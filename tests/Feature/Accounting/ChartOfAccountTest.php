<?php

declare(strict_types=1);

namespace Tests\Feature\Accounting;

use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartOfAccountTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_index_displays_accounts(): void
    {
        ChartOfAccount::factory()->count(3)->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->get(route('accounts.index'));

        $response->assertOk();
        $response->assertSee('Chart of Accounts');
    }

    public function test_account_can_be_created(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->post(route('accounts.store'), [
            'company_id' => $company->id,
            'code' => '1000',
            'name' => 'Cash',
            'type' => 'asset',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('chart_of_accounts', ['code' => '1000']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('accounts.store'), []);

        $response->assertSessionHasErrors(['company_id', 'code', 'name', 'type']);
    }

    public function test_store_validates_unique_code_per_company(): void
    {
        $company = Company::factory()->create();
        ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '1000']);

        $response = $this->actingAs($this->user)->post(route('accounts.store'), [
            'company_id' => $company->id,
            'code' => '1000',
            'name' => 'Cash',
            'type' => 'asset',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_account_can_be_updated(): void
    {
        $account = ChartOfAccount::factory()->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->put(route('accounts.update', $account), [
            'code' => $account->code,
            'name' => 'Updated Account',
            'type' => $account->type,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('chart_of_accounts', ['id' => $account->id, 'name' => 'Updated Account']);
    }

    public function test_account_can_be_deleted(): void
    {
        $account = ChartOfAccount::factory()->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($this->user)->delete(route('accounts.destroy', $account));

        $response->assertRedirect();
        $this->assertSoftDeleted('chart_of_accounts', ['id' => $account->id]);
    }
}
