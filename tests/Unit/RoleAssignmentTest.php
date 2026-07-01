<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAssignmentTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Role Assignment
    |--------------------------------------------------------------------------
    */

    public function test_user_can_be_assigned_a_role(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);

        $user->assignRole($role);

        $this->assertTrue($user->hasRole('Editor'));
    }

    public function test_user_can_be_assigned_role_by_name(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'Viewer', 'guard_name' => 'web']);

        $user->assignRole('Viewer');

        $this->assertTrue($user->hasRole('Viewer'));
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();
        $role1 = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);

        $user->assignRole([$role1, $role2]);

        $this->assertTrue($user->hasRole('Editor'));
        $this->assertTrue($user->hasRole('Viewer'));
        $this->assertEquals(2, $user->roles->count());
    }

    public function test_role_can_be_removed_from_user(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);

        $user->assignRole($role);
        $this->assertTrue($user->hasRole('Editor'));

        $user->removeRole($role);
        $this->assertFalse($user->hasRole('Editor'));
    }

    public function test_roles_can_be_synced(): void
    {
        $user = User::factory()->create();
        $role1 = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);
        $role3 = Role::create(['name' => 'Admin', 'guard_name' => 'web']);

        $user->assignRole([$role1, $role2]);
        $this->assertEquals(2, $user->roles->count());

        $user->syncRoles([$role2, $role3]);
        $this->assertEquals(2, $user->roles->count());
        $this->assertFalse($user->hasRole('Editor'));
        $this->assertTrue($user->hasRole('Viewer'));
        $this->assertTrue($user->hasRole('Admin'));
    }

    /*
    |--------------------------------------------------------------------------
    | Role Checking
    |--------------------------------------------------------------------------
    */

    public function test_has_role_returns_true_when_user_has_role(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function test_has_role_returns_false_when_user_lacks_role(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'Admin', 'guard_name' => 'web']);

        $this->assertFalse($user->hasRole('Admin'));
    }

    public function test_has_any_role_returns_true_when_user_has_at_least_one(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->assertTrue($user->hasAnyRole(['Admin', 'Editor']));
    }

    public function test_has_any_role_returns_false_when_user_has_none(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Editor', 'guard_name' => 'web']);

        $this->assertFalse($user->hasAnyRole(['Admin', 'Editor']));
    }

    public function test_has_all_roles_returns_true_when_user_has_all(): void
    {
        $user = User::factory()->create();
        $role1 = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);
        $user->assignRole([$role1, $role2]);

        $this->assertTrue($user->hasAllRoles(['Editor', 'Viewer']));
    }

    public function test_has_all_roles_returns_false_when_user_lacks_one(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $user->assignRole($role);
        Role::create(['name' => 'Admin', 'guard_name' => 'web']);

        $this->assertFalse($user->hasAllRoles(['Editor', 'Admin']));
    }

    /*
    |--------------------------------------------------------------------------
    | Role Properties
    |--------------------------------------------------------------------------
    */

    public function test_role_has_name(): void
    {
        $role = Role::create(['name' => 'Administrator', 'guard_name' => 'web']);

        $this->assertEquals('Administrator', $role->name);
    }

    public function test_role_has_guard_name(): void
    {
        $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);

        $this->assertEquals('web', $role->guard_name);
    }

    public function test_role_can_be_found_by_name(): void
    {
        Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        $found = Role::findByName('Manager');

        $this->assertNotNull($found);
        $this->assertEquals('Manager', $found->name);
    }

    public function test_role_name_must_be_unique_per_guard(): void
    {
        Role::create(['name' => 'Admin', 'guard_name' => 'web']);

        $this->expectException(RoleAlreadyExists::class);

        Role::create(['name' => 'Admin', 'guard_name' => 'web']);
    }
}
