<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['auth', 'permission:users.view'])->get('/test-users-view', fn () => 'ok')->name('test.users.view');
        Route::middleware(['auth', 'permission:users.view,users.create'])->get('/test-users-any', fn () => 'ok')->name('test.users.any');
    }

    /*
    |--------------------------------------------------------------------------
    | Access Granted
    |--------------------------------------------------------------------------
    */

    public function test_user_with_required_permission_can_access(): void
    {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'users.view', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $response = $this->actingAs($user)->get('/test-users-view');

        $response->assertOk();
    }

    public function test_user_with_permission_via_role_can_access(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'users.view', 'guard_name' => 'web']);

        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/test-users-view');

        $response->assertOk();
    }

    public function test_user_with_any_of_multiple_permissions_can_access(): void
    {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'users.create', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $response = $this->actingAs($user)->get('/test-users-any');

        $response->assertOk();
    }

    /*
    |--------------------------------------------------------------------------
    | Access Denied
    |--------------------------------------------------------------------------
    */

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/test-users-view');

        $response->assertStatus(403);
    }

    public function test_user_with_wrong_permission_gets_403(): void
    {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'reports.view', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $response = $this->actingAs($user)->get('/test-users-view');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_gets_redirect(): void
    {
        $response = $this->get('/test-users-view');

        $response->assertRedirect('/login');
    }
}
