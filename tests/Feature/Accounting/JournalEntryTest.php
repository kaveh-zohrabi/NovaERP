<?php

declare(strict_types=1);

namespace Tests\Feature\Accounting;

use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
    }

    public function test_index_displays_entries(): void
    {
        JournalEntry::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('journal-entries.index'));

        $response->assertOk();
        $response->assertSee('Journal Entries');
    }

    public function test_entry_can_be_created(): void
    {
        $debitAccount = ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'code' => '1000', 'type' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'code' => '4000', 'type' => 'revenue']);

        $response = $this->actingAs($this->user)->post(route('journal-entries.store'), [
            'company_id' => $this->company->id,
            'date' => now()->toDateString(),
            'description' => 'Test journal entry',
            'lines' => [
                ['account_id' => $debitAccount->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 100],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('journal_entries', ['status' => 'draft']);
    }

    public function test_unbalanced_entry_fails(): void
    {
        $debitAccount = ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'code' => '1000', 'type' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'code' => '4000', 'type' => 'revenue']);

        $response = $this->actingAs($this->user)->post(route('journal-entries.store'), [
            'company_id' => $this->company->id,
            'date' => now()->toDateString(),
            'description' => 'Unbalanced entry',
            'lines' => [
                ['account_id' => $debitAccount->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 50],
            ],
        ]);

        $response->assertSessionHas('error');
    }

    public function test_entry_can_be_posted(): void
    {
        $debitAccount = ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'code' => '1000', 'type' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'code' => '4000', 'type' => 'revenue']);

        $entry = JournalEntry::factory()->create(['company_id' => $this->company->id]);
        $entry->lines()->create(['account_id' => $debitAccount->id, 'debit' => 100, 'credit' => 0]);
        $entry->lines()->create(['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 100]);

        $response = $this->actingAs($this->user)->patch(route('journal-entries.post', $entry));

        $response->assertRedirect();
        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id, 'status' => 'posted']);
    }
}
