<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyServiceTest extends TestCase
{
    use RefreshDatabase;

    private CompanyService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(CompanyService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function test_create_returns_company_instance(): void
    {
        $user = User::factory()->create();

        $company = $this->service->create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'email' => 'info@acme.com',
            'status' => 'active',
        ], $user);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals('Acme Corp', $company->name);
    }

    public function test_create_assigns_creator(): void
    {
        $user = User::factory()->create();

        $company = $this->service->create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'email' => 'info@acme.com',
            'status' => 'active',
        ], $user);

        $this->assertTrue($company->fresh()->users->contains($user));
    }

    public function test_create_stores_logo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $logo = UploadedFile::fake()->image('logo.jpg');

        $company = $this->service->create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'email' => 'info@acme.com',
            'logo' => $logo,
            'status' => 'active',
        ], $user);

        $this->assertNotNull($company->logo);
        Storage::disk('public')->assertExists($company->logo);
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_update_returns_fresh_company(): void
    {
        $company = Company::factory()->create();

        $result = $this->service->update($company, [
            'name' => 'Updated Corp',
            'slug' => $company->slug,
            'email' => 'updated@example.com',
            'status' => 'active',
        ]);

        $this->assertEquals('Updated Corp', $result->name);
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'Updated Corp']);
    }

    public function test_update_handles_logo_upload(): void
    {
        Storage::fake('public');
        $company = Company::factory()->create();
        $logo = UploadedFile::fake()->image('new-logo.jpg');

        $result = $this->service->update($company, [
            'name' => $company->name,
            'slug' => $company->slug,
            'email' => $company->email,
            'logo' => $logo,
            'status' => 'active',
        ]);

        $this->assertNotNull($result->logo);
        Storage::disk('public')->assertExists($result->logo);
    }

    public function test_update_preserves_logo_when_no_new_logo(): void
    {
        $company = Company::factory()->create(['logo' => 'logos/old.jpg']);

        $result = $this->service->update($company, [
            'name' => $company->name,
            'slug' => $company->slug,
            'email' => $company->email,
            'status' => 'active',
        ]);

        $this->assertEquals('logos/old.jpg', $result->logo);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate
    |--------------------------------------------------------------------------
    */

    public function test_activate_changes_status_to_active(): void
    {
        $company = Company::factory()->inactive()->create();

        $result = $this->service->activate($company);

        $this->assertTrue($result);
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'status' => 'active']);
    }

    public function test_activate_returns_false_if_already_active(): void
    {
        $company = Company::factory()->create();

        $result = $this->service->activate($company);

        $this->assertFalse($result);
    }

    public function test_deactivate_changes_status_to_inactive(): void
    {
        $company = Company::factory()->create();

        $result = $this->service->deactivate($company);

        $this->assertTrue($result);
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'status' => 'inactive']);
    }

    public function test_deactivate_returns_false_if_already_inactive(): void
    {
        $company = Company::factory()->inactive()->create();

        $result = $this->service->deactivate($company);

        $this->assertFalse($result);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function test_delete_soft_deletes_company(): void
    {
        $company = Company::factory()->create();

        $result = $this->service->delete($company);

        $this->assertTrue($result['success']);
        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_delete_prevents_deletion_with_users(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $company->users()->attach($user);

        $result = $this->service->delete($company);

        $this->assertFalse($result['success']);
        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function test_restore_restores_soft_deleted_company(): void
    {
        $company = Company::factory()->create();
        $company->delete();

        $result = $this->service->restore($company);

        $this->assertTrue($result);
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'deleted_at' => null]);
    }

    public function test_restore_returns_false_if_not_trashed(): void
    {
        $company = Company::factory()->create();

        $result = $this->service->restore($company);

        $this->assertFalse($result);
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function test_force_delete_removes_company(): void
    {
        $company = Company::factory()->create();

        $this->service->forceDelete($company);

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_force_delete_detaches_users(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $company->users()->attach($user);

        $this->service->forceDelete($company);

        $this->assertDatabaseMissing('company_user', ['company_id' => $company->id]);
    }
}
