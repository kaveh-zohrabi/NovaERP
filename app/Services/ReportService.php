<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ReportDefinition;
use App\Models\ReportExecution;
use App\Support\BaseService;

class ReportService extends BaseService
{
    public function __construct(
        private readonly SalesReportService $salesReport,
        private readonly InventoryReportService $inventoryReport,
        private readonly FinancialReportService $financialReport,
    ) {}

    public function generateReport(ReportDefinition $report, array $filters, int $executedBy): ReportExecution
    {
        $execution = ReportExecution::create([
            'report_definition_id' => $report->id,
            'executed_by' => $executedBy,
            'filters' => $filters,
            'status' => 'processing',
        ]);

        try {
            $data = $this->executeReport($report->type, $filters);
            $execution->update(['status' => 'completed']);

            return $execution->fresh();
        } catch (\Exception $e) {
            $execution->update(['status' => 'failed']);

            throw $e;
        }
    }

    public function executeReport(string $type, array $filters = []): array
    {
        return match ($type) {
            'sales_overview' => $this->salesReport->getOverview($filters),
            'product_sales' => $this->salesReport->getByProduct($filters),
            'customer_sales' => $this->salesReport->getByCustomer($filters),
            'inventory_valuation' => $this->inventoryReport->getValuation($filters),
            'low_stock' => $this->inventoryReport->getLowStock($filters),
            'stock_movements' => $this->inventoryReport->getMovements($filters),
            'profit_loss' => $this->financialReport->getProfitAndLoss($filters),
            'balance_sheet' => $this->financialReport->getBalanceSheet($filters),
            'trial_balance' => $this->financialReport->getTrialBalance($filters),
            default => throw new \InvalidArgumentException("Unknown report type: {$type}"),
        };
    }

    public function getAvailableReports(int $companyId): array
    {
        return [
            ['type' => 'sales_overview', 'name' => 'Sales Overview', 'category' => 'Sales'],
            ['type' => 'product_sales', 'name' => 'Product Sales', 'category' => 'Sales'],
            ['type' => 'customer_sales', 'name' => 'Customer Sales', 'category' => 'Sales'],
            ['type' => 'inventory_valuation', 'name' => 'Inventory Valuation', 'category' => 'Inventory'],
            ['type' => 'low_stock', 'name' => 'Low Stock Report', 'category' => 'Inventory'],
            ['type' => 'stock_movements', 'name' => 'Stock Movements', 'category' => 'Inventory'],
            ['type' => 'profit_loss', 'name' => 'Profit & Loss', 'category' => 'Accounting'],
            ['type' => 'balance_sheet', 'name' => 'Balance Sheet', 'category' => 'Accounting'],
            ['type' => 'trial_balance', 'name' => 'Trial Balance', 'category' => 'Accounting'],
        ];
    }
}
