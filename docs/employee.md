# Employee Management Module

## Overview

The Employee Management module handles employee records, assignments, and lifecycle within the ERP system.

---

## Architecture

```
EmployeeController → EmployeeService → Employee Model
     ↑                    ↑
     │                    │
Form Requests        Database
(validation)         (Eloquent)
```

### Responsibilities

| Component | Responsibility |
|-----------|----------------|
| **Controller** | HTTP handling, redirects, flash messages |
| **Service** | Business logic, assignments, lifecycle |
| **FormRequest** | Validation rules and error messages |
| **Model** | Relationships, scopes, accessors, helpers |

---

## Database Schema

### `employees` Table

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint | Primary key |
| `company_id` | bigint | FK → companies.id |
| `branch_id` | bigint | FK → branches.id (nullable) |
| `department_id` | bigint | FK → departments.id (nullable) |
| `position_id` | bigint | FK → positions.id (nullable) |
| `user_id` | bigint | FK → users.id (nullable, unique) |
| `employee_code` | varchar | Unique employee code |
| `first_name` | varchar | First name |
| `last_name` | varchar | Last name |
| `email` | varchar | Email (unique) |
| `phone` | varchar | Phone number |
| `date_of_birth` | date | Date of birth |
| `hire_date` | date | Hire date |
| `termination_date` | date | Termination date |
| `status` | varchar | active/inactive/suspended/terminated |
| `employment_type` | varchar | full_time/part_time/contract/intern |
| `salary` | decimal | Salary amount |
| `avatar` | varchar | Avatar file path |
| `metadata` | json | Additional metadata |

---

## EmployeeService Methods

| Method | Purpose |
|--------|---------|
| `create($data, $creator)` | Create new employee |
| `update($employee, $data)` | Update employee details |
| `assign($employee, $assignments)` | Assign to branch/department/position |
| `terminate($employee, $date)` | Terminate employment |
| `reactivate($employee)` | Reactivate terminated employee |
| `delete($employee)` | Soft delete |
| `restore($employee)` | Restore soft-deleted |

---

## Enums

### EmployeeStatus

| Value | Label | CSS Class |
|-------|-------|-----------|
| `active` | Active | green |
| `inactive` | Inactive | gray |
| `suspended` | Suspended | yellow |
| `terminated` | Terminated | red |

### EmploymentType

| Value | Label |
|-------|-------|
| `full_time` | Full Time |
| `part_time` | Part Time |
| `contract` | Contract |
| `intern` | Intern |

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/employees` | List employees |
| GET | `/employees/create` | Create form |
| POST | `/employees` | Store employee |
| GET | `/employees/{employee}` | Show employee |
| GET | `/employees/{employee}/edit` | Edit form |
| PUT | `/employees/{employee}` | Update employee |
| DELETE | `/employees/{employee}` | Soft delete |
| POST | `/employees/{employee}/restore` | Restore |
| PATCH | `/employees/{employee}/terminate` | Terminate |
| PATCH | `/employees/{employee}/reactivate` | Reactivate |

---

## Tests

| Test Suite | Tests |
|------------|-------|
| `EmployeeTest` | 14 |
| `EmployeeServiceTest` | 9 |
| **Total** | **23** |

### Running Tests

```bash
php artisan test --filter=Employee
```
