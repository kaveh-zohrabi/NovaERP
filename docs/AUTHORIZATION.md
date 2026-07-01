# Authorization Module Documentation

## Overview

NovaERP uses **Spatie Laravel Permission** for role-based access control (RBAC). The authorization system provides:

- **Roles** — Named collections of permissions
- **Permissions** — Granular access rights
- **Policies** — Model-based authorization
- **Gates** — Non-resource authorization
- **Middleware** — Route-level authorization

---

## Architecture

```
User ──has many──> Role ──has many──> Permission
  │                                    │
  └──has many (direct)─────────────────┘
```

| Component | Purpose | Location |
|-----------|---------|----------|
| Roles | Group permissions by job function | `roles` table |
| Permissions | Individual access rights | `permissions` table |
| Policies | Authorize model operations | `app/Policies/` |
| Gates | Authorize non-model operations | `app/Providers/AuthServiceProvider.php` |
| Middleware | Authorize route access | `app/Http/Middleware/` |

---

## Roles

### Definition

A role is a **named collection of permissions** that represents a job function.

### Default Roles

| Role | Purpose | Permission Count |
|------|---------|------------------|
| **Administrator** | Full system access | 24 |
| **Manager** | Day-to-day operations | 15 |
| **Employee** | Standard user tasks | 7 |

### Usage

```php
// Assign role
$user->assignRole('Administrator');

// Check role
$user->hasRole('Administrator');        // true
$user->hasAnyRole(['Admin', 'Manager']); // true
$user->hasAllRoles(['Admin', 'Manager']); // false

// Remove role
$user->removeRole('Administrator');

// Sync roles (replace all)
$user->syncRoles(['Manager', 'Employee']);
```

### Route Middleware

```php
// Single role
Route::middleware('role:Administrator')->group(...);

// Multiple roles (OR logic)
Route::middleware('role:Administrator,Manager')->group(...);
```

---

## Permissions

### Naming Convention

```
{module}.{action}
```

### Default Permissions

| Module | Permissions |
|--------|-------------|
| **Users** | `users.view`, `users.create`, `users.update`, `users.delete`, `users.manage` |
| **Roles** | `roles.view`, `roles.manage` |
| **Permissions** | `permissions.view`, `permissions.manage` |
| **Profile** | `profile.view`, `profile.update`, `profile.change-password` |
| **Sessions** | `sessions.view`, `sessions.force-logout` |
| **Invoices** | `invoices.view`, `invoices.create`, `invoices.update`, `invoices.delete` |
| **Inventory** | `inventory.view`, `inventory.adjust` |
| **Reports** | `reports.view`, `reports.export` |
| **Settings** | `settings.view`, `settings.manage` |

### The `manage` Permission

Each module has a `manage` permission that acts as a wildcard:

```
users.manage = users.view + users.create + users.update + users.delete
```

### Usage

```php
// Direct permission
$user->givePermissionTo('settings.view');
$user->revokePermissionTo('settings.view');

// Check permission
$user->hasPermissionTo('settings.view');
$user->hasAnyPermission(['settings.view', 'settings.manage']);
$user->hasAllPermissions(['settings.view', 'settings.manage']);

// Get all permissions (from roles + direct)
$user->getAllPermissions();
```

### Route Middleware

```php
// Single permission
Route::middleware('permission:users.view')->group(...);

// Multiple permissions (OR logic)
Route::middleware('permission:users.view,users.create')->group(...);

// Multiple permissions (AND logic)
Route::middleware('permission.all:users.view,users.create')->group(...);
```

---

## Policies

### Definition

Policies authorize **model-based operations** (CRUD). They answer: "Can this user perform this action on this model?"

### BasePolicy

All policies extend `BasePolicy` which provides:

| Method | Purpose |
|--------|---------|
| `permissionPrefix()` | Defines the permission prefix (e.g., `users`) |
| `before()` | Super admin bypass |
| `hasPermission()` | Checks `{prefix}.{action}` permission |

### UserPolicy

| Method | Logic | Permission Required |
|--------|-------|---------------------|
| `viewAny()` | View user list | `users.view` OR `users.manage` |
| `view()` | View a user | Own profile OR `users.view` OR `users.manage` |
| `create()` | Create users | `users.create` OR `users.manage` |
| `update()` | Update a user | Own profile OR `users.update` OR `users.manage` |
| `delete()` | Delete a user | Own account OR `users.delete` OR `users.manage` |
| `restore()` | Restore a user | `users.delete` OR `users.manage` |
| `forceDelete()` | Permanently delete | `users.delete` OR `users.manage` |

### Usage

```php
// In controller
$this->authorize('update', $request->user());

// In Blade
@can('update', $user)
    <a href="{{ route('users.edit', $user) }}">Edit</a>
@endcan

// In policy
public function update(User $user, User $model): bool
{
    return $user->is($model)
        || $this->hasPermission($user, 'update')
        || $this->hasPermission($user, 'manage');
}
```

### Creating New Policies

```bash
php artisan make:policy InvoicePolicy --model=Invoice
```

Then extend `BasePolicy`:

```php
class InvoicePolicy extends BasePolicy
{
    protected function permissionPrefix(): string
    {
        return 'invoices';
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create');
    }
}
```

Register in `AuthServiceProvider`:

```php
protected $policies = [
    User::class => UserPolicy::class,
    Invoice::class => InvoicePolicy::class,
];
```

---

## Gates

### Definition

Gates authorize **non-model operations**. They answer: "Can this user perform this action?"

### When to Use Gates

| Use Case | Why Gate |
|----------|----------|
| Settings management | No Settings model exists |
| Report access | Reports are aggregates, not records |
| Session management | Infrastructure, not domain model |
| Role/Permission management | Group operations, not CRUD |

### Registered Gates

| Gate | Permissions | Purpose |
|------|-------------|---------|
| `view-settings` | `settings.view` OR `settings.manage` | View system configuration |
| `manage-settings` | `settings.manage` | Edit system configuration |
| `view-reports` | `reports.view` OR `reports.export` | View report dashboards |
| `export-reports` | `reports.export` | Export reports to PDF/CSV |
| `view-sessions` | `sessions.view` | View active sessions |
| `force-logout` | `sessions.force-logout` | Force logout other users |
| `manage-roles` | `roles.view` OR `roles.manage` | View/manage roles |
| `manage-permissions` | `permissions.view` OR `permissions.manage` | View/manage permissions |

### Usage

```php
// In controller
$this->authorize('manage-settings');

// In Blade
@can('view-reports')
    <a href="{{ route('reports.index') }}">Reports</a>
@endcan

// In Gate definition
Gate::define('view-settings', function (User $user) {
    return $user->hasAnyPermission(['settings.view', 'settings.manage']);
});
```

### Creating New Gates

```php
// In AuthServiceProvider
Gate::define('export-invoices', function (User $user) {
    try {
        return $user->hasPermissionTo('invoices.export');
    } catch (\Throwable) {
        return false;
    }
});
```

---

## Middleware

### Available Middleware

| Middleware | Alias | Purpose |
|------------|-------|---------|
| `RoleMiddleware` | `role` | Check if user has required role |
| `PermissionMiddleware` | `permission` | Check if user has any of the permissions |
| `EnsureAllPermissionsMiddleware` | `permission.all` | Check if user has ALL permissions |
| `EnsureUserIsActive` | `active` | Block suspended/inactive users |

### Usage

```php
// Role check
Route::middleware('role:Administrator')->group(...);
Route::middleware('role:Administrator,Manager')->group(...);

// Permission check (OR logic)
Route::middleware('permission:users.view')->group(...);
Route::middleware('permission:users.view,users.create')->group(...);

// Permission check (AND logic)
Route::middleware('permission.all:users.view,users.create')->group(...);

// Active user check
Route::middleware('active')->group(...);

// Combined
Route::middleware(['auth', 'active', 'role:Administrator'])->group(...);
```

### Important

Middleware must be used with `auth` middleware:

```php
// Correct
Route::middleware(['auth', 'role:Administrator'])->group(...);

// Wrong (will fail)
Route::middleware('role:Administrator')->group(...);
```

---

## Gates vs Policies

| Aspect | Gate | Policy |
|--------|------|--------|
| **Tied to model** | No | Yes |
| **Use case** | Non-resource operations | Resource operations (CRUD) |
| **Registration** | `Gate::define()` | `$policies` array |
| **Usage** | `$user->can('gate-name')` | `$user->can('action', $model)` |
| **Examples** | Settings, Reports, Sessions | User, Product, Invoice |

---

## Super Admin Bypass

Users with the `super-admin` role bypass all permission checks:

```php
// In BasePolicy
public function before(User $user, string $ability): ?bool
{
    if ($user->hasRole('super-admin')) {
        return true;
    }

    return null;
}
```

---

## Testing

### Run Authorization Tests

```bash
php artisan test --filter=RoleAssignmentTest
php artisan test --filter=PermissionAssignmentTest
php artisan test --filter=UserPolicyTest
php artisan test --filter=GateTest
php artisan test --filter=RoleMiddlewareTest
php artisan test --filter=PermissionMiddlewareTest
```

### Test Coverage

| Test File | Tests | Coverage |
|-----------|-------|----------|
| `RoleAssignmentTest` | 14 | Role assignment, checking, properties |
| `PermissionAssignmentTest` | 16 | Permission assignment, checking, direct vs role |
| `UserPolicyTest` | 15 | Policy-based authorization |
| `GateTest` | 23 | Gate-based authorization |
| `RoleMiddlewareTest` | 5 | Middleware authorization for roles |
| `PermissionMiddlewareTest` | 6 | Middleware authorization for permissions |
