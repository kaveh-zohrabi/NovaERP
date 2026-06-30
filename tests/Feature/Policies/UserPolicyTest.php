<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function createUserWithRole(string $roleSlug, array $permissionSlugs = []): User
    {
        $role = Role::create([
            'name' => ucfirst($roleSlug),
            'slug' => $roleSlug,
        ]);

        foreach ($permissionSlugs as $slug) {
            $perm = Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => ucfirst(str_replace('.', ' ', $slug)), 'group' => explode('.', $slug)[0]]
            );
            $role->permissions()->attach($perm->id);
        }

        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        return $user->load('roles.permissions');
    }

    /*
    |--------------------------------------------------------------------------
    | viewAny
    |--------------------------------------------------------------------------
    */

    public function test_user_with_view_permission_can_view_any_user(): void
    {
        $user = $this->createUserWithRole('viewer', ['users.view']);

        $this->assertTrue($user->can('viewAny', User::class));
    }

    public function test_user_without_view_permission_cannot_view_any_user(): void
    {
        $user = $this->createUserWithRole('limited', []);

        $this->assertFalse($user->can('viewAny', User::class));
    }

    /*
    |--------------------------------------------------------------------------
    | view
    |--------------------------------------------------------------------------
    */

    public function test_user_can_view_own_profile(): void
    {
        $user = $this->createUserWithRole('limited', []);

        $this->assertTrue($user->can('view', $user));
    }

    public function test_user_with_view_permission_can_view_other_user(): void
    {
        $user = $this->createUserWithRole('viewer', ['users.view']);
        $other = User::factory()->create();

        $this->assertTrue($user->can('view', $other));
    }

    public function test_user_without_permission_cannot_view_other_user(): void
    {
        $user = $this->createUserWithRole('limited', []);
        $other = User::factory()->create();

        $this->assertFalse($user->can('view', $other));
    }

    /*
    |--------------------------------------------------------------------------
    | create
    |--------------------------------------------------------------------------
    */

    public function test_user_with_create_permission_can_create_users(): void
    {
        $user = $this->createUserWithRole('creator', ['users.create']);

        $this->assertTrue($user->can('create', User::class));
    }

    public function test_user_without_create_permission_cannot_create_users(): void
    {
        $user = $this->createUserWithRole('limited', []);

        $this->assertFalse($user->can('create', User::class));
    }

    /*
    |--------------------------------------------------------------------------
    | update
    |--------------------------------------------------------------------------
    */

    public function test_user_can_always_update_own_profile(): void
    {
        $user = $this->createUserWithRole('limited', []);

        $this->assertTrue($user->can('update', $user));
    }

    public function test_user_with_update_permission_can_update_other_user(): void
    {
        $user = $this->createUserWithRole('editor', ['users.update']);
        $other = User::factory()->create();

        $this->assertTrue($user->can('update', $other));
    }

    public function test_user_without_permission_cannot_update_other_user(): void
    {
        $user = $this->createUserWithRole('limited', []);
        $other = User::factory()->create();

        $this->assertFalse($user->can('update', $other));
    }

    /*
    |--------------------------------------------------------------------------
    | delete
    |--------------------------------------------------------------------------
    */

    public function test_user_can_always_delete_own_account(): void
    {
        $user = $this->createUserWithRole('limited', []);

        $this->assertTrue($user->can('delete', $user));
    }

    public function test_user_with_delete_permission_can_delete_other_user(): void
    {
        $user = $this->createUserWithRole('deleter', ['users.delete']);
        $other = User::factory()->create();

        $this->assertTrue($user->can('delete', $other));
    }

    public function test_user_without_permission_cannot_delete_other_user(): void
    {
        $user = $this->createUserWithRole('limited', []);
        $other = User::factory()->create();

        $this->assertFalse($user->can('delete', $other));
    }

    /*
    |--------------------------------------------------------------------------
    | manage (wildcard permission)
    |--------------------------------------------------------------------------
    */

    public function test_user_with_manage_permission_can_do_everything(): void
    {
        $user = $this->createUserWithRole('admin', ['users.manage']);
        $other = User::factory()->create();

        $this->assertTrue($user->can('viewAny', User::class));
        $this->assertTrue($user->can('view', $other));
        $this->assertTrue($user->can('create', User::class));
        $this->assertTrue($user->can('update', $other));
        $this->assertTrue($user->can('delete', $other));
    }

    /*
    |--------------------------------------------------------------------------
    | Unauthenticated
    |--------------------------------------------------------------------------
    */

    public function test_unauthenticated_user_cannot_access_user_actions(): void
    {
        $this->assertGuest();

        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }
}
