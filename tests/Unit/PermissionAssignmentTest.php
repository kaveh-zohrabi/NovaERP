<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionAssignmentTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Direct Permission Assignment
    |--------------------------------------------------------------------------
    */

    public function test_user_can_be_assigned_direct_permission(): void
    {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'articles.publish', 'guard_name' => 'web']);

        $user->givePermissionTo($permission);

        $this->assertTrue($user->hasPermissionTo('articles.publish'));
    }

    public function test_user_can_be_assigned_permission_by_name(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'articles.edit', 'guard_name' => 'web']);

        $user->givePermissionTo('articles.edit');

        $this->assertTrue($user->hasPermissionTo('articles.edit'));
    }

    public function test_user_can_have_multiple_direct_permissions(): void
    {
        $user = User::factory()->create();
        $perm1 = Permission::create(['name' => 'articles.create', 'guard_name' => 'web']);
        $perm2 = Permission::create(['name' => 'articles.edit', 'guard_name' => 'web']);

        $user->givePermissionTo([$perm1, $perm2]);

        $this->assertTrue($user->hasPermissionTo('articles.create'));
        $this->assertTrue($user->hasPermissionTo('articles.edit'));
    }

    public function test_direct_permission_can_be_revoked(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'articles.delete', 'guard_name' => 'web']);

        $user->givePermissionTo('articles.delete');
        $this->assertTrue($user->hasPermissionTo('articles.delete'));

        $user->revokePermissionTo('articles.delete');
        $this->assertFalse($user->hasPermissionTo('articles.delete'));
    }

    /*
    |--------------------------------------------------------------------------
    | Permission via Role
    |--------------------------------------------------------------------------
    */

    public function test_user_gets_permission_through_role(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'posts.edit', 'guard_name' => 'web']);

        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->assertTrue($user->hasPermissionTo('posts.edit'));
    }

    public function test_user_lacks_permission_when_role_lacks_it(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);
        Permission::create(['name' => 'posts.edit', 'guard_name' => 'web']);

        $user->assignRole($role);

        $this->assertFalse($user->hasPermissionTo('posts.edit'));
    }

    public function test_user_gets_permissions_from_multiple_roles(): void
    {
        $user = User::factory()->create();
        $role1 = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'Publisher', 'guard_name' => 'web']);
        $perm1 = Permission::create(['name' => 'posts.edit', 'guard_name' => 'web']);
        $perm2 = Permission::create(['name' => 'posts.publish', 'guard_name' => 'web']);

        $role1->givePermissionTo($perm1);
        $role2->givePermissionTo($perm2);
        $user->assignRole([$role1, $role2]);

        $this->assertTrue($user->hasPermissionTo('posts.edit'));
        $this->assertTrue($user->hasPermissionTo('posts.publish'));
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Checking
    |--------------------------------------------------------------------------
    */

    public function test_has_permission_to_returns_true_when_user_has_it(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'reports.view', 'guard_name' => 'web']);
        $user->givePermissionTo('reports.view');

        $this->assertTrue($user->hasPermissionTo('reports.view'));
    }

    public function test_has_permission_to_returns_false_when_user_lacks_it(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'reports.view', 'guard_name' => 'web']);

        $this->assertFalse($user->hasPermissionTo('reports.view'));
    }

    public function test_has_any_permission_returns_true_when_user_has_at_least_one(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'reports.view', 'guard_name' => 'web']);
        $user->givePermissionTo('reports.view');

        $this->assertTrue($user->hasAnyPermission(['reports.view', 'reports.export']));
    }

    public function test_has_any_permission_returns_false_when_user_has_none(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'reports.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'reports.export', 'guard_name' => 'web']);

        $this->assertFalse($user->hasAnyPermission(['reports.view', 'reports.export']));
    }

    public function test_has_all_permissions_returns_true_when_user_has_all(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'reports.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'reports.export', 'guard_name' => 'web']);
        $user->givePermissionTo(['reports.view', 'reports.export']);

        $this->assertTrue($user->hasAllPermissions(['reports.view', 'reports.export']));
    }

    public function test_has_all_permissions_returns_false_when_user_lacks_one(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'reports.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'reports.export', 'guard_name' => 'web']);
        $user->givePermissionTo('reports.view');

        $this->assertFalse($user->hasAllPermissions(['reports.view', 'reports.export']));
    }

    /*
    |--------------------------------------------------------------------------
    | Direct vs Role Permissions
    |--------------------------------------------------------------------------
    */

    public function test_direct_permission_not_affected_by_role_removal(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $perm1 = Permission::create(['name' => 'posts.edit', 'guard_name' => 'web']);
        $perm2 = Permission::create(['name' => 'posts.publish', 'guard_name' => 'web']);

        $role->givePermissionTo($perm1);
        $user->assignRole($role);
        $user->givePermissionTo($perm2);

        $user->removeRole($role);

        $this->assertFalse($user->hasPermissionTo('posts.edit'));
        $this->assertTrue($user->hasPermissionTo('posts.publish'));
    }

    public function test_get_all_permissions_merges_role_and_direct(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $perm1 = Permission::create(['name' => 'posts.edit', 'guard_name' => 'web']);
        $perm2 = Permission::create(['name' => 'posts.publish', 'guard_name' => 'web']);

        $role->givePermissionTo($perm1);
        $user->assignRole($role);
        $user->givePermissionTo($perm2);

        $allPermissions = $user->getAllPermissions()->pluck('name');

        $this->assertTrue($allPermissions->contains('posts.edit'));
        $this->assertTrue($allPermissions->contains('posts.publish'));
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Properties
    |--------------------------------------------------------------------------
    */

    public function test_permission_has_name(): void
    {
        $permission = Permission::create(['name' => 'users.manage', 'guard_name' => 'web']);

        $this->assertEquals('users.manage', $permission->name);
    }

    public function test_permission_has_guard_name(): void
    {
        $permission = Permission::create(['name' => 'users.view', 'guard_name' => 'web']);

        $this->assertEquals('web', $permission->guard_name);
    }

    public function test_permission_can_be_found_by_name(): void
    {
        Permission::create(['name' => 'settings.view', 'guard_name' => 'web']);

        $found = Permission::findByName('settings.view');

        $this->assertNotNull($found);
        $this->assertEquals('settings.view', $found->name);
    }
}
