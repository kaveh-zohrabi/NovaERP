# Audit Log & Activity Log Module

## Overview

The Audit module provides centralized, immutable, searchable and enterprise-grade auditing across the entire ERP. It uses Event-Driven Architecture with Observers, Events, and Listeners to automatically record every important business event.

---

## Architecture

```
Business Modules
    ↓
Model Observers → Dispatch Events
    ↓
CreateAuditLogListener (Queued)
    ↓
AuditService
    ↓
audit_logs table (immutable)
```

---

## Core Entities

| Entity | Purpose |
|--------|---------|
| AuditLog | Detailed change tracking (old/new values, IP, user agent) |
| ActivityLog | High-level activity timeline (what happened, when, who) |

---

## Event Flow

1. Model changes detected by `AuditObserver`
2. Observer dispatches `ModelAuditableEvent`
3. `CreateAuditLogListener` receives event (queued)
4. `AuditService::logEvent()` writes to `audit_logs` table
5. Record is immutable after creation

---

## Services

| Service | Purpose |
|---------|---------|
| AuditService | Create audit records, query history, advanced filtering |
| ActivityService | Record activities, query activity timeline |
| AuditExportService | Export audit data to CSV/PDF |
| AuditQueryService | Advanced filtering and search |

---

## Observed Models

| Model | Events Tracked |
|-------|---------------|
| User | created, updated, deleted, restored |
| Company | created, updated, deleted |
| Product | created, updated, deleted |
| Customer | created, updated, deleted |
| PurchaseOrder | created, updated, deleted |
| SalesOrder | created, updated, deleted |
| Invoice | created, updated, deleted |
| JournalEntry | created, updated, deleted |
| Lead | created, updated, deleted |
| Opportunity | created, updated, deleted |
| Stock | created, updated, deleted |

---

## Audit Events

created, updated, deleted, restored, approved, rejected, posted, cancelled, assigned, unassigned, login, logout, password_changed, permission_changed, role_assigned, role_removed

---

## Immutability

- Audit records cannot be updated
- Audit records cannot be deleted
- History is permanent
- No soft deletes on audit tables

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/audit` | Audit log list |
| GET | `/audit/{auditLog}` | Audit detail |
| GET | `/audit/activity` | Activity timeline |
| GET | `/audit/entity-history` | Entity change history |
| GET | `/audit/export` | Export audit data |

---

## Filtering

Supports:
- Event type
- User
- Entity type + ID
- Date range
- IP address
- Full-text search

---

## Export

- CSV export with all audit fields
- PDF export support
- Filtered export (respects current filters)
- Max 10,000 records per export

---

## Performance

- Database indexes on all filter columns
- JSON indexes for old_values/new_values
- Queue-based audit writing (non-blocking)
- Chunk processing for large exports

---

## Tests

| Test Suite | Tests |
|------------|-------|
| AuditTest | 5 |
| AuditServiceTest | 6 |
| ActivityServiceTest | 4 |
| **Total** | **15** |
