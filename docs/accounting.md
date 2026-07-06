# Accounting System Module

## Overview

The Accounting module provides double-entry bookkeeping integrated with Sales, Purchasing, and Inventory modules.

---

## Double-Entry Rule

Every journal entry must satisfy: **TOTAL DEBIT = TOTAL CREDIT**

No exceptions. The system enforces this at the service layer.

---

## Architecture

```
ChartOfAccount ──has many──> JournalEntryLine ──belongs to──> JournalEntry
                                        │
                                        └── belongs to ──> ChartOfAccount

JournalEntry ──has many──> JournalEntryLine
```

---

## Chart of Accounts

| Type | Code Range | Purpose |
|------|------------|---------|
| Asset | 1xxx | Cash, AR, Inventory |
| Liability | 2xxx | AP, Tax Payable |
| Equity | 3xxx | Owner's Equity |
| Revenue | 4xxx | Sales, Service Revenue |
| Expense | 5xxx | COGS, Salaries, Rent |

---

## Journal Entry Lifecycle

```
1. Create Entry (Draft) — validate balanced lines
2. Post Entry — make immutable
3. Reverse Entry — create opposite entry
```

### Rules

| Rule | Enforcement |
|------|-------------|
| Must be balanced | Service validates debit = credit |
| Minimum 2 lines | Service validates line count |
| Draft → Posted | Only draft entries can be posted |
| Posted → Reversed | Only posted entries can be reversed |
| Posted entries | Immutable |

---

## Services

| Service | Purpose |
|---------|---------|
| `ChartOfAccountService` | CRUD chart of accounts |
| `JournalEntryService` | Create, post, reverse journal entries |
| `AccountingPostingService` | Post invoices and purchases to accounting |

---

## Integration

### Sales → Accounting
```
Invoice Created:
  DR Accounts Receivable (1200)
  CR Revenue (4000)
```

### Purchasing → Accounting
```
Purchase Order:
  DR Inventory (1300)
  CR Accounts Payable (2000)
```

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/accounts` | List chart of accounts |
| POST | `/accounts` | Store account |
| GET | `/accounts/{account}` | Show account |
| PUT | `/accounts/{account}` | Update account |
| DELETE | `/accounts/{account}` | Delete account |
| GET | `/journal-entries` | List journal entries |
| POST | `/journal-entries` | Store journal entry |
| GET | `/journal-entries/{entry}` | Show journal entry |
| PATCH | `/journal-entries/{entry}/post` | Post entry |
| PATCH | `/journal-entries/{entry}/reverse` | Reverse entry |

---

## Tests

| Test Suite | Tests |
|------------|-------|
| `ChartOfAccountTest` | 6 |
| `JournalEntryTest` | 4 |
| `JournalEntryServiceTest` | 5 |
| **Total** | **15** |
