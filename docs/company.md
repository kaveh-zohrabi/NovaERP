# Company Module

## Overview

The Company module manages organizations within NovaERP. Each company represents a business entity with its own users, settings, and data.

---

## Architecture

```
CompanyController → CompanyService → Company Model
     ↑                    ↑
     │                    │
Form Requests        Database
(validation)         (Eloquent)
```

### Responsibilities

| Component | Responsibility |
|-----------|----------------|
| **Controller** | HTTP handling, redirects, flash messages |
| **Service** | Business logic, transactions, file uploads |
| **FormRequest** | Validation rules and error messages |
| **Model** | Relationships, scopes, accessors, helpers |

---

## Database Schema

### `companies` Table

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `name` | varchar | Display name |
| `slug` | varchar | URL-friendly identifier (unique) |
| `legal_name` | varchar | Official name for documents |
| `registration_number` | varchar | Government registration |
| `tax_number` | varchar | Tax identification |
| `email` | varchar | Primary contact |
| `phone` | varchar | Contact phone |
| `website` | varchar | Company website |
| `address` | text | Street address |
| `city` | varchar | City |
| `state` | varchar | State/Province |
| `country` | varchar | Country (ISO code) |
| `postal_code` | varchar | ZIP/Postal code |
| `logo` | varchar | Logo file path |
| `status` | varchar | active/inactive |
| `settings` | json | Company-specific config |
| `created_by` | bigint | FK → users.id |
| `updated_by` | bigint | FK → users.id |
| `deleted_at` | timestamp | Soft delete |

### `company_user` Pivot

| Column | Type | Purpose |
|--------|------|---------|
| `company_id` | bigint | FK → companies.id |
| `user_id` | bigint | FK → users.id |
| `is_default` | boolean | User's active company |

---

## CompanyService Methods

| Method | Purpose |
|--------|---------|
| `create($data, $creator)` | Create company + assign creator |
| `update($company, $data)` | Update company + handle logo |
| `activate($company)` | Set status to active |
| `deactivate($company)` | Set status to inactive |
| `delete($company)` | Soft delete (prevents if has users) |
| `restore($company)` | Restore soft-deleted company |
| `forceDelete($company)` | Permanently delete + cleanup |

---

## Validation Rules

### Required Fields

| Field | Rules |
|-------|-------|
| `name` | required, string, max:255 |
| `slug` | required, string, unique, alpha_dash |
| `email` | required, email |
| `status` | required, in:active,inactive |

### Optional Fields

| Field | Rules |
|-------|-------|
| `legal_name` | nullable, string, max:255 |
| `registration_number` | nullable, string, max:255 |
| `tax_number` | nullable, string, max:255 |
| `phone` | nullable, string, max:255 |
| `website` | nullable, url, max:255 |
| `address` | nullable, string, max:1000 |
| `city` | nullable, string, max:255 |
| `state` | nullable, string, max:255 |
| `country` | nullable, string, max:2 |
| `postal_code` | nullable, string, max:20 |
| `logo` | nullable, image, max:2048 |
| `settings` | nullable, array |

---

## Soft Deletes

Companies use soft deletes to prevent accidental data loss.

| Operation | Behavior |
|-----------|----------|
| `delete()` | Sets `deleted_at` timestamp |
| `restore()` | Clears `deleted_at` |
| `forceDelete()` | Permanently removes record + logo |

**Protection:** Cannot soft delete a company with users attached.

---

## File Uploads

| Operation | Behavior |
|-----------|----------|
| Create with logo | Stores in `storage/app/public/logos/` |
| Update with new logo | Deletes old, stores new |
| Update without logo | Preserves existing |
| Force delete | Deletes logo from storage |

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/companies` | List companies |
| GET | `/companies/create` | Create form |
| POST | `/companies` | Store company |
| GET | `/companies/{company}` | Show company |
| GET | `/companies/{company}/edit` | Edit form |
| PUT | `/companies/{company}` | Update company |
| DELETE | `/companies/{company}` | Soft delete |
| POST | `/companies/{company}/restore` | Restore |
| DELETE | `/companies/{company}/force-delete` | Permanent delete |
| PATCH | `/companies/{company}/activate` | Activate |
| PATCH | `/companies/{company}/deactivate` | Deactivate |

---

## Tests

| Test Suite | Tests | Coverage |
|------------|-------|----------|
| `CompanyTest` | 18 | Full CRUD, search, activate/deactivate, soft delete, restore, force delete |
| `CompanyServiceTest` | 16 | Service methods, logo handling, user protection |

### Running Tests

```bash
php artisan test --filter=Company
```
