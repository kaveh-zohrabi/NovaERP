# Reporting & Business Intelligence Module

## Overview

The Reporting module provides executive dashboards, business reports, and analytics across all ERP modules. It reads data without modifying business logic.

---

## Architecture

```
ERP Modules (Sales, Inventory, Accounting, CRM, Purchasing)
    ↓
Report Services (AnalyticsService, ReportService, FinancialReportService, SalesReportService, InventoryReportService)
    ↓
Controllers (DashboardController, ReportController)
    ↓
Dashboard & Report UI (Blade views)
```

---

## Core Entities

| Entity | Purpose |
|--------|---------|
| Dashboard | Customizable management dashboards |
| DashboardWidget | Individual dashboard components |
| ReportDefinition | Stored report configurations |
| ReportExecution | Generated report tracking |

---

## Services

| Service | Purpose |
|---------|---------|
| DashboardService | Manage dashboards and widgets |
| ReportService | Generate reports, apply filters, route to sub-services |
| AnalyticsService | Aggregate executive metrics |
| ExportService | CSV and PDF export |
| FinancialReportService | P&L, Balance Sheet, Trial Balance |
| SalesReportService | Sales overview, product/customer sales |
| InventoryReportService | Valuation, low stock, movements |

---

## Report Types

### Sales
- Sales Overview (daily/monthly/yearly)
- Product Sales (best/slow selling)
- Customer Sales (top customers)

### Inventory
- Inventory Valuation
- Low Stock Report
- Stock Movement History
- Fast-Moving Products

### Accounting
- Profit & Loss Statement
- Balance Sheet
- Trial Balance

### CRM
- Lead Conversion Rate
- Opportunity Pipeline

---

## Executive Dashboard Metrics

| Metric | Source |
|--------|--------|
| Total Revenue | Paid invoices |
| Total Expenses | Approved POs |
| Net Profit | Revenue - Expenses |
| Customer Count | Customers table |
| Lead Conversion Rate | Leads converted / total leads |
| Low Stock Count | Stock below reorder level |
| Pipeline Value | Open opportunity expected values |

---

## Filter System

Reports support:
- Date range (start_date, end_date)
- Company
- Product
- Customer

---

## Export System

| Format | Method |
|--------|--------|
| CSV | ExportService::exportCsv() |
| PDF | ExportService::exportPdf() |

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/executive` | Executive dashboard |
| GET | `/dashboards` | List dashboards |
| POST | `/dashboards` | Create dashboard |
| GET | `/dashboards/{dashboard}` | Show dashboard |
| DELETE | `/dashboards/{dashboard}` | Delete dashboard |
| GET | `/reports` | List available reports |
| GET | `/reports/{type}` | Show report |
| GET | `/reports/{type}/export` | Export report |

---

## Tests

| Test Suite | Tests |
|------------|-------|
| DashboardTest | 4 |
| ReportTest | 6 |
| AnalyticsServiceTest | 2 |
| ReportServiceTest | 6 |
| **Total** | **18** |

---

## Performance Notes

- Financial reports query posted journal entries only
- Inventory valuation joins through warehouses for company filtering
- Export generates files on-demand (queue-ready for large datasets)
- Heavy reports can be cached via service layer
