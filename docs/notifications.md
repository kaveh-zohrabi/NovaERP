# Notification & Communication Center

## Overview

The Notification module provides a centralized communication system for the entire ERP. It supports in-app notifications, email delivery, user preferences, and template-based messaging.

---

## Architecture

```
ERP Modules (events)
    ↓
NotificationService (create, queue, deduplicate)
    ↓
EmailNotificationService (send via Mail)
    ↓
User In-App Notifications (user_notifications table)
```

---

## Core Entities

| Entity | Purpose |
|--------|---------|
| UserNotification | Individual notification records |
| NotificationTemplate | Reusable message templates with variables |
| NotificationPreference | Per-user channel/type preferences |

---

## Notification Flow

1. ERP module triggers event
2. `NotificationService::create()` stores in-app notification
3. `EmailNotificationService::send()` queues email delivery
4. User preferences checked before sending
5. Duplicate prevention via 5-minute window

---

## Services

| Service | Purpose |
|---------|---------|
| NotificationService | Create, mark read, archive, delete, search, duplicate detection |
| NotificationTemplateService | CRUD templates, render with variables |
| NotificationPreferenceService | User preference CRUD, check channel enabled |
| EmailNotificationService | Email delivery with template rendering |

---

## Trigger Events

| Module | Event | Type |
|--------|-------|------|
| Inventory | Low stock | `low_stock` |
| Purchasing | Purchase created/approved | `purchase_created`, `purchase_approved` |
| Sales | Order created, invoice issued | `order_created`, `invoice_issued` |
| Accounting | Journal entry posted | `journal_posted` |
| CRM | New lead, opportunity won/lost | `new_lead`, `opportunity_won`, `opportunity_lost` |
| Documents | File uploaded | `file_uploaded` |
| Auth | Login, password changed | `login_alert`, `password_changed` |

---

## Channels

| Channel | Status |
|---------|--------|
| In-App | Implemented |
| Email | Implemented |
| SMS | Future |
| WhatsApp | Future |
| Slack | Future |

---

## User Preferences

Users can enable/disable per:
- Channel (email, in_app)
- Notification type (low_stock, new_lead, etc.)

Default: all enabled.

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/notifications` | List notifications |
| GET | `/notifications/unread-count` | Get unread count (JSON) |
| GET | `/notifications/{notification}` | Show notification |
| PATCH | `/notifications/{notification}/read` | Mark as read |
| PATCH | `/notifications/read-all` | Mark all as read |
| PATCH | `/notifications/{notification}/archive` | Archive |
| DELETE | `/notifications/{notification}` | Delete |

---

## Queue Strategy

- Email sent via `Mail::raw()` (queue-ready)
- Duplicate prevention: 5-minute dedup window per user/type/title
- Retry: Laravel queue retry on failure
- Failures logged via exception handling

---

## Tests

| Test Suite | Tests |
|------------|-------|
| NotificationTest | 8 |
| NotificationServiceTest | 7 |
| NotificationPreferenceServiceTest | 5 |
| **Total** | **20** |
