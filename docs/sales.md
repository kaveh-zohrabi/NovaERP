# Sales System Module

## Overview

The Sales module manages customers, sales orders, invoices, and payments.

---

## Architecture

```
Customer ──has many──> SalesOrder ──has many──> SalesOrderItem
    │                      │
    │                      └──has many──> Invoice ──has many──> InvoiceItem
    │                                        │
    │                                        └──has many──> Payment
    │
    └── triggers ──> StockMovement (OUT) via Inventory module
```

---

## Sales Workflow

```
1. Create Sales Order (Draft)
2. Add Items to Sales Order
3. Confirm Sales Order
4. Generate Invoice
5. Ship / Deliver Goods
6. Trigger StockMovement (OUT)
7. Register Payment
```

### Sales Order States

| State | Description |
|-------|-------------|
| `draft` | Can be edited, items can be added/removed |
| `confirmed` | Ready for invoicing |
| `invoiced` | Invoice generated |
| `partially_shipped` | Some items shipped |
| `shipped` | All items shipped |
| `cancelled` | Cannot proceed |

---

## Business Rules

| Rule | Enforcement |
|------|-------------|
| Only draft orders can be edited | SalesOrderService |
| Customer must be active | Customer validation |
| Invoice is immutable after issuance | InvoiceService |
| Cancelled orders cannot be invoiced | InvoiceService |
| Stock decreases only at shipment | StockMovementService |

---

## Services

| Service | Purpose |
|---------|---------|
| `CustomerService` | CRUD customers |
| `SalesOrderService` | Create, confirm, cancel, manage items |
| `InvoiceService` | Generate invoices, mark paid/cancelled |

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/customers` | List customers |
| POST | `/customers` | Store customer |
| GET | `/customers/{customer}` | Show customer |
| PUT | `/customers/{customer}` | Update customer |
| DELETE | `/customers/{customer}` | Delete customer |
| GET | `/orders` | List sales orders |
| POST | `/orders` | Store sales order |
| GET | `/orders/{order}` | Show sales order |
| PATCH | `/orders/{order}/confirm` | Confirm order |
| PATCH | `/orders/{order}/cancel` | Cancel order |
| GET | `/invoices` | List invoices |
| GET | `/invoices/{invoice}` | Show invoice |
| POST | `/orders/{order}/invoice` | Generate invoice |
| PATCH | `/invoices/{invoice}/paid` | Mark paid |
| PATCH | `/invoices/{invoice}/cancel` | Cancel invoice |

---

## Tests

| Test Suite | Tests |
|------------|-------|
| `CustomerTest` | 5 |
| `SalesOrderTest` | 5 |
| **Total** | **10** |
