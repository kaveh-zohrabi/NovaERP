# Purchasing System Module

## Overview

The Purchasing module manages suppliers, purchase orders, goods receipts, and stock integration.

---

## Architecture

```
Supplier ──has many──> PurchaseOrder ──has many──> PurchaseOrderItem
                              │
                              └──has many──> GoodsReceipt ──has many──> GoodsReceiptItem
                                                    │
                                                    └──triggers──> StockMovement (IN)
```

---

## Purchase Workflow

```
1. Create Purchase Order (Draft)
2. Add Items to Purchase Order
3. Approve Purchase Order
4. Receive Goods (GoodsReceipt)
5. Generate StockMovement (IN) from GoodsReceipt
```

### Purchase Order States

| State | Description |
|-------|-------------|
| `draft` | Can be edited, items can be added/removed |
| `approved` | Immutable, ready for goods receipt |
| `partially_received` | Some items received |
| `completed` | All items received |
| `cancelled` | Cannot be used for receiving |

---

## Business Rules

| Rule | Enforcement |
|------|-------------|
| Only draft orders can be edited | PurchaseOrderService |
| Approved orders are immutable | PurchaseOrderService |
| Supplier must be active | Supplier validation |
| Warehouse must exist | Warehouse validation |
| Cannot receive more than ordered | GoodsReceiptService |
| Stock increases only through GoodsReceipt | StockMovementService |

---

## Services

| Service | Purpose |
|---------|---------|
| `SupplierService` | CRUD suppliers |
| `PurchaseOrderService` | Create, approve, cancel, manage items |
| `GoodsReceiptService` | Receive goods, create stock movements |

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/suppliers` | List suppliers |
| POST | `/suppliers` | Store supplier |
| GET | `/suppliers/{supplier}` | Show supplier |
| PUT | `/suppliers/{supplier}` | Update supplier |
| DELETE | `/suppliers/{supplier}` | Delete supplier |
| GET | `/orders` | List purchase orders |
| POST | `/orders` | Store purchase order |
| GET | `/orders/{order}` | Show purchase order |
| PUT | `/orders/{order}` | Update purchase order |
| PATCH | `/orders/{order}/approve` | Approve order |
| PATCH | `/orders/{order}/cancel` | Cancel order |
| GET | `/orders/{order}/receive` | Receive goods form |
| POST | `/orders/{order}/receive` | Record goods receipt |

---

## Tests

| Test Suite | Tests |
|------------|-------|
| `SupplierTest` | 5 |
| `PurchaseOrderTest` | 5 |
| **Total** | **10** |
