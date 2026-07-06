# Organization Structure Module

## Overview

The Organization Structure module defines the hierarchical structure of a company:

- **Branches** — Physical locations (offices, warehouses, stores)
- **Departments** — Functional units within a branch (Sales, HR, IT)
- **Positions** — Job titles within departments (Manager, Analyst, Clerk)

---

## Architecture

```
Company ──has many──> Branch ──has many──> Department ──has many──> Position
   │                    │                      │
   └──has many──> User ─┴──────────────────────┘
```

### Responsibilities

| Component | Responsibility |
|-----------|----------------|
| **Controller** | HTTP handling, redirects, flash messages |
| **Service** | Business logic, transactions, status management |
| **FormRequest** | Validation rules and error messages |
| **Model** | Relationships, scopes, helpers |

---

## Database Schema

### `branches`

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `company_id` | bigint | FK → companies.id |
| `name` | varchar | Branch name |
| `slug` | varchar | URL-friendly (unique per company) |
| `code` | varchar | Business code (unique per company) |
| `email` | varchar | Contact email |
| `phone` | varchar | Contact phone |
| `address` | text | Physical address |
| `city` | varchar | City |
| `state` | varchar | State/Province |
| `country` | varchar | Country code |
| `postal_code` | varchar | ZIP code |
| `status` | varchar | active/inactive |
| `is_headquarters` | boolean | Main branch flag |

### `departments`

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `branch_id` | bigint | FK → branches.id |
| `company_id` | bigint | FK → companies.id (denormalized) |
| `name` | varchar | Department name |
| `slug` | varchar | URL-friendly (unique per branch) |
| `code` | varchar | Business code |
| `description` | text | Department description |
| `status` | varchar | active/inactive |

### `positions`

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `department_id` | bigint | FK → departments.id |
| `company_id` | bigint | FK → companies.id (denormalized) |
| `name` | varchar | Position title |
| `slug` | varchar | URL-friendly identifier |
| `code` | varchar | Business code |
| `description` | text | Position description |
| `min_salary` | decimal | Minimum salary |
| `max_salary` | decimal | Maximum salary |
| `status` | varchar | active/inactive |

---

## Relationships

| Relationship | Cardinality |
|-------------|-------------|
| Company → Branch | 1 : N |
| Branch → Department | 1 : N |
| Department → Position | 1 : N |
| User → Branch | N : 1 |
| User → Department | N : 1 |
| User → Position | N : 1 |

---

## Services

| Service | Methods |
|---------|---------|
| `BranchService` | create, update, activate, deactivate, delete, restore |
| `DepartmentService` | create, update, activate, deactivate, delete, restore |
| `PositionService` | create, update, activate, deactivate, delete, restore |

---

## Routes

| Method | URI | Entity |
|--------|-----|--------|
| GET | `/branches` | List branches |
| GET | `/branches/create` | Create form |
| POST | `/branches` | Store branch |
| GET | `/branches/{branch}` | Show branch |
| GET | `/branches/{branch}/edit` | Edit form |
| PUT | `/branches/{branch}` | Update branch |
| DELETE | `/branches/{branch}` | Soft delete |
| POST | `/branches/{branch}/restore` | Restore |
| PATCH | `/branches/{branch}/activate` | Activate |
| PATCH | `/branches/{branch}/deactivate` | Deactivate |
| * | (Same pattern for departments and positions) | |

---

## Validation

### Branch
- `name`: required, max:255
- `slug`: required, unique per company, alpha_dash
- `code`: nullable, unique per company
- `email`: nullable, email
- `status`: required, active/inactive

### Department
- `name`: required, max:255
- `slug`: required, unique per branch, alpha_dash
- `branch_id`: required, exists:branches
- `company_id`: required, exists:companies
- `status`: required, active/inactive

### Position
- `name`: required, max:255
- `department_id`: required, exists:departments
- `company_id`: required, exists:companies
- `min_salary`: nullable, numeric, min:0
- `max_salary`: nullable, numeric, min:0, gte:min_salary
- `status`: required, active/inactive

---

## Tests

| Test Suite | Tests |
|------------|-------|
| `BranchTest` | 14 |
| `BranchServiceTest` | 9 |
| `DepartmentTest` | 12 |
| `DepartmentServiceTest` | 9 |
| `PositionTest` | 10 |
| `PositionServiceTest` | 9 |
| **Total** | **63** |

### Running Tests

```bash
php artisan test --filter=Branch
php artisan test --filter=Department
php artisan test --filter=Position
```
