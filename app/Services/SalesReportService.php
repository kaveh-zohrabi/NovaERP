<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Support\BaseService;

class SalesReportService extends BaseService
{
    public function getOverview(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;
        $startDate = $filters['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $filters['end_date'] ?? now()->endOfMonth()->toDateString();

        $orders = SalesOrder::where('company_id', $companyId)
            ->whereBetween('order_date', [$startDate, $endDate]);

        $dailySales = SalesOrder::where('company_id', $companyId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->selectRaw('order_date, COUNT(*) as order_count, SUM(total_amount) as total')
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get();

        $paidInvoices = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate]);

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_orders' => $orders->count(),
            'total_order_value' => (float) $orders->sum('total_amount'),
            'paid_invoices_count' => $paidInvoices->count(),
            'total_revenue' => (float) $paidInvoices->sum('total_amount'),
            'daily_sales' => $dailySales->toArray(),
        ];
    }

    public function getByProduct(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;
        $startDate = $filters['start_date'] ?? now()->startOfYear()->toDateString();
        $endDate = $filters['end_date'] ?? now()->endOfYear()->toDateString();

        $productSales = \App\Models\SalesOrderItem::whereHas('salesOrder', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
                ->whereBetween('order_date', [$startDate, $endDate]);
        })
            ->join('products', 'sales_order_items.product_id', '=', 'products.id')
            ->selectRaw('products.id, products.name, products.sku, SUM(sales_order_items.quantity) as total_quantity, SUM(sales_order_items.quantity * sales_order_items.unit_price) as total_revenue')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->get();

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'products' => $productSales->toArray(),
        ];
    }

    public function getByCustomer(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;
        $startDate = $filters['start_date'] ?? now()->startOfYear()->toDateString();
        $endDate = $filters['end_date'] ?? now()->endOfYear()->toDateString();

        $customerSales = SalesOrder::where('company_id', $companyId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->join('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->selectRaw('customers.id, customers.name, COUNT(*) as order_count, SUM(sales_orders.total_amount) as total_spent')
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->get();

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'customers' => $customerSales->toArray(),
        ];
    }

    public function getTopEmployees(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? 1;
        $startDate = $filters['start_date'] ?? now()->startOfYear()->toDateString();
        $endDate = $filters['end_date'] ?? now()->endOfYear()->toDateString();

        $employeeSales = SalesOrder::where('company_id', $companyId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereNotNull('created_by')
            ->join('users', 'sales_orders.created_by', '=', 'users.id')
            ->selectRaw('users.id, users.name, COUNT(*) as order_count, SUM(sales_orders.total_amount) as total_sales')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->get();

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'employees' => $employeeSales->toArray(),
        ];
    }
}
