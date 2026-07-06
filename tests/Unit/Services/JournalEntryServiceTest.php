<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\User;
use App\Services\JournalEntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalEntryServiceTest extends TestCase
{
    use RefreshDatabase;

    private JournalEntryService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(JournalEntryService::class);
    }

    public function test_create_returns_journal_entry(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $debitAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '1000', 'type' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '4000', 'type' => 'revenue']);

        $entry = $this->service->create(
            [
                'company_id' => $company->id,
                'date' => now()->toDateString(),
                'description' => 'Test entry',
            ],
            [
                ['account_id' => $debitAccount->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 100],
            ],
            $user
        );

        $this->assertInstanceOf(JournalEntry::class, $entry);
        $this->assertEquals('draft', $entry->status);
        $this->assertEquals(2, $entry->lines()->count());
    }

    public function test_unbalanced_entry_throws_exception(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $debitAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '1000', 'type' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '4000', 'type' => 'revenue']);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->create(
            [
                'company_id' => $company->id,
                'date' => now()->toDateString(),
                'description' => 'Unbalanced',
            ],
            [
                ['account_id' => $debitAccount->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 50],
            ],
            $user
        );
    }

    public function test_post_sets_status_to_posted(): void
    {
        $company = Company::factory()->create();
        $debitAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '1000', 'type' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '4000', 'type' => 'revenue']);

        $entry = JournalEntry::factory()->create(['company_id' => $company->id]);
        $entry->lines()->create(['account_id' => $debitAccount->id, 'debit' => 100, 'credit' => 0]);
        $entry->lines()->create(['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 100]);

        $result = $this->service->post($entry);

        $this->assertTrue($result);
        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id, 'status' => 'posted']);
    }

    public function test_post_throws_on_unbalanced_entry(): void
    {
        $company = Company::factory()->create();
        $debitAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '1000', 'type' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '4000', 'type' => 'revenue']);

        $entry = JournalEntry::factory()->create(['company_id' => $company->id]);
        $entry->lines()->create(['account_id' => $debitAccount->id, 'debit' => 100, 'credit' => 0]);
        $entry->lines()->create(['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 50]);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->post($entry);
    }

    public function test_post_throws_on_draft_entry(): void
    {
        $company = Company::factory()->create();
        $entry = JournalEntry::factory()->create(['company_id' => $company->id, 'status' => 'draft']);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->post($entry);
    }

    public function test_reverse_creates_opposite_entry(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $debitAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '1000', 'type' => 'asset']);
        $creditAccount = ChartOfAccount::factory()->create(['company_id' => $company->id, 'code' => '4000', 'type' => 'revenue']);

        $entry = JournalEntry::factory()->create(['company_id' => $company->id, 'status' => 'posted']);
        $entry->lines()->create(['account_id' => $debitAccount->id, 'debit' => 100, 'credit' => 0, 'description' => 'Sale']);
        $entry->lines()->create(['account_id' => $creditAccount->id, 'debit' => 0, 'credit' => 100, 'description' => 'Revenue']);

        $this->actingAs($user);

        $reversal = $this->service->reverse($entry);

        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id, 'status' => 'reversed']);
        $this->assertDatabaseHas('journal_entries', ['id' => $reversal->id, 'status' => 'draft']);
        $this->assertEquals(2, $reversal->lines()->count());
    }
}
