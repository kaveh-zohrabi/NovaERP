<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GateTest extends TestCase
{
    use RefreshDatabase;

    private function createPermission(string $name): Permission
    {
        return Permission::create(['name' => $name, 'guard_name' => 'web']);
    }

    /*
    |--------------------------------------------------------------------------
    | Settings Gates
    |--------------------------------------------------------------------------
    */

    public function test_user_with_settings_view_can_view_settings(): void
    {
        $user = User::factory()->create();
        $this->createPermission('settings.view');
        $user->givePermissionTo('settings.view');

        $this->assertTrue($user->can('view-settings'));
    }

    public function test_user_with_settings_manage_can_view_settings(): void
    {
        $user = User::factory()->create();
        $this->createPermission('settings.manage');
        $user->givePermissionTo('settings.manage');

        $this->assertTrue($user->can('view-settings'));
    }

    public function test_user_without_settings_permission_cannot_view_settings(): void
    {
        $user = User::factory()->create();
        $this->createPermission('settings.view');

        $this->assertFalse($user->can('view-settings'));
    }

    public function test_user_with_settings_manage_can_manage_settings(): void
    {
        $user = User::factory()->create();
        $this->createPermission('settings.manage');
        $user->givePermissionTo('settings.manage');

        $this->assertTrue($user->can('manage-settings'));
    }

    public function test_user_with_settings_view_cannot_manage_settings(): void
    {
        $user = User::factory()->create();
        $this->createPermission('settings.view');
        $user->givePermissionTo('settings.view');

        $this->assertFalse($user->can('manage-settings'));
    }

    /*
    |--------------------------------------------------------------------------
    | Report Gates
    |--------------------------------------------------------------------------
    */

    public function test_user_with_reports_view_can_view_reports(): void
    {
        $user = User::factory()->create();
        $this->createPermission('reports.view');
        $user->givePermissionTo('reports.view');

        $this->assertTrue($user->can('view-reports'));
    }

    public function test_user_with_reports_export_can_view_reports(): void
    {
        $user = User::factory()->create();
        $this->createPermission('reports.export');
        $user->givePermissionTo('reports.export');

        $this->assertTrue($user->can('view-reports'));
    }

    public function test_user_without_reports_permission_cannot_view_reports(): void
    {
        $user = User::factory()->create();
        $this->createPermission('reports.view');

        $this->assertFalse($user->can('view-reports'));
    }

    public function test_user_with_reports_export_can_export_reports(): void
    {
        $user = User::factory()->create();
        $this->createPermission('reports.export');
        $user->givePermissionTo('reports.export');

        $this->assertTrue($user->can('export-reports'));
    }

    public function test_user_with_reports_view_cannot_export_reports(): void
    {
        $user = User::factory()->create();
        $this->createPermission('reports.view');
        $user->givePermissionTo('reports.view');

        $this->assertFalse($user->can('export-reports'));
    }

    /*
    |--------------------------------------------------------------------------
    | Session Gates
    |--------------------------------------------------------------------------
    */

    public function test_user_with_sessions_view_can_view_sessions(): void
    {
        $user = User::factory()->create();
        $this->createPermission('sessions.view');
        $user->givePermissionTo('sessions.view');

        $this->assertTrue($user->can('view-sessions'));
    }

    public function test_user_without_sessions_permission_cannot_view_sessions(): void
    {
        $user = User::factory()->create();
        $this->createPermission('sessions.view');

        $this->assertFalse($user->can('view-sessions'));
    }

    public function test_user_with_force_logout_can_force_logout(): void
    {
        $user = User::factory()->create();
        $this->createPermission('sessions.force-logout');
        $user->givePermissionTo('sessions.force-logout');

        $this->assertTrue($user->can('force-logout'));
    }

    public function test_user_without_force_logout_cannot_force_logout(): void
    {
        $user = User::factory()->create();
        $this->createPermission('sessions.force-logout');

        $this->assertFalse($user->can('force-logout'));
    }

    /*
    |--------------------------------------------------------------------------
    | Role & Permission Gates
    |--------------------------------------------------------------------------
    */

    public function test_user_with_roles_view_can_manage_roles(): void
    {
        $user = User::factory()->create();
        $this->createPermission('roles.view');
        $user->givePermissionTo('roles.view');

        $this->assertTrue($user->can('manage-roles'));
    }

    public function test_user_with_roles_manage_can_manage_roles(): void
    {
        $user = User::factory()->create();
        $this->createPermission('roles.manage');
        $user->givePermissionTo('roles.manage');

        $this->assertTrue($user->can('manage-roles'));
    }

    public function test_user_without_roles_permission_cannot_manage_roles(): void
    {
        $user = User::factory()->create();
        $this->createPermission('roles.view');

        $this->assertFalse($user->can('manage-roles'));
    }

    public function test_user_with_permissions_view_can_manage_permissions(): void
    {
        $user = User::factory()->create();
        $this->createPermission('permissions.view');
        $user->givePermissionTo('permissions.view');

        $this->assertTrue($user->can('manage-permissions'));
    }

    public function test_user_with_permissions_manage_can_manage_permissions(): void
    {
        $user = User::factory()->create();
        $this->createPermission('permissions.manage');
        $user->givePermissionTo('permissions.manage');

        $this->assertTrue($user->can('manage-permissions'));
    }

    public function test_user_without_permissions_permission_cannot_manage_permissions(): void
    {
        $user = User::factory()->create();
        $this->createPermission('permissions.view');

        $this->assertFalse($user->can('manage-permissions'));
    }

    /*
    |--------------------------------------------------------------------------
    | Gates Work Through Roles
    |--------------------------------------------------------------------------
    */

    public function test_gate_works_through_role_permissions(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $permission = $this->createPermission('settings.manage');

        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->assertTrue($user->can('manage-settings'));
    }
}
