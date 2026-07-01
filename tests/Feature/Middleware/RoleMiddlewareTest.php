<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['auth', 'role:Administrator'])->get('/test-admin', fn () => 'ok')->name('test.admin');
        Route::middleware(['auth', 'role:Administrator,Manager'])->get('/test-admin-or-manager', fn () => 'ok')->name('test.admin.or.manager');
    }

    /*
    |--------------------------------------------------------------------------
    | Access Granted
    |--------------------------------------------------------------------------
    */

    public function test_user_with_required_role_can_access(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Administrator', 'guard_name' => 'web']);
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/test-admin');

        $response->assertOk();
    }

    public function test_user_with_any_of_multiple_roles_can_access(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/test-admin-or-manager');

        $response->assertOk();
    }

    /*
    |--------------------------------------------------------------------------
    | Access Denied
    |--------------------------------------------------------------------------
    */

    public function test_user_without_required_role_gets_403(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Employee', 'guard_name' => 'web']);
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/test-admin');

        $response->assertStatus(403);
    }

    public function test_user_with_no_roles_gets_403(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/test-admin');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_gets_redirect(): void
    {
        $response = $this->get('/test-admin');

        $response->assertRedirect('/login');
    }
}
