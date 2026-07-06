# Inventory Management Module

## Overview

The Inventory module manages products, warehouses, and stock levels. Stock changes are tracked through immutable StockMovement records.

---

## Architecture

```
Product ──belongs to──> Stock ──has many──> StockMovement
                              │
Warehouse ──has many──────────┘
```

---

## Stock System Rules

### Core Principle

**Stock MUST NEVER be updated directly.**

All stock changes go through `StockMovementService` which:
1. Validates the operation
2. Creates a StockMovement record (immutable audit trail)
3. Updates Stock quantity

### Movement Types

| Type | Effect | Use Case |
|------|--------|----------|
| `IN` | Increases stock | Purchase, return |
| `OUT` | Decreases stock | Sale, damage |
| `TRANSFER` | Moves between warehouses | Warehouse A → B |
| `ADJUSTMENT` | Corrects stock | Inventory count |

### Business Rules

| Rule | Enforcement |
|------|-------------|
| Stock cannot go negative (OUT) | Validation in StockService |
| Product SKU unique per company | Database constraint |
| Warehouse code unique per company | Database constraint |
| StockMovements are immutable | No update/delete methods |
| ADJUSTMENT requires notes | Validation in StockService |

---

## Services

| Service | Purpose |
|---------|---------|
| `ProductService` | CRUD products |
| `StockService` | Read stock, check availability |
| `StockMovementService` | Create movements, update stock |

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/products` | List products |
| GET | `/products/create` | Create form |
| POST | `/products` | Store product |
| GET | `/products/{product}` | Show product |
| GET | `/products/{product}/edit` | Edit form |
| PUT | `/products/{product}` | Update product |
| DELETE | `/products/{product}` | Delete product |
| GET | `/stock` | Stock overview |
| GET | `/stock/{stock}` | Stock details |
| GET | `/stock-movements` | Movement history |
| GET | `/stock-movements/create` | Record movement form |
| POST | `/stock-movements` | Record movement |

---

## Tests

| Test Suite | Tests |
|------------|-------|
| `ProductTest` | 7 |
| `StockMovementTest` | 8 |
| `StockServiceTest` | 3 |
| **Total** | **18** |
