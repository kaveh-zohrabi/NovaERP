<?php

declare(strict_types=1);

namespace App\Services;

use App\Support\BaseService;

class AnalyticsService extends BaseService
{
    public function getExecutiveMetrics(int $companyId, ?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? now()->parse($startDate)->startOfDay() : now()->startOfMonth();
        $end = $endDate ? now()->parse($endDate)->endOfDay() : now()->endOfDay();

        return [
            'revenue' => $this->getRevenue($companyId, $start, $end),
            'expenses' => $this->getExpenses($companyId, $start, $end),
            'net_profit' => $this->getRevenue($companyId, $start, $end) - $this->getExpenses($companyId, $start, $end),
            'total_customers' => $this->getCustomerCount($companyId),
            'total_leads' => $this->getLeadCount($companyId),
            'lead_conversion_rate' => $this->getLeadConversionRate($companyId),
            'total_products' => $this->getProductCount($companyId),
            'low_stock_count' => $this->getLowStockCount($companyId),
            'open_opportunities' => $this->getOpenOpportunityCount($companyId),
            'opportunity_value' => $this->getOpenOpportunityValue($companyId),
        ];
    }

    private function getRevenue(int $companyId, $start, $end): float
    {
        return (float) \App\Models\Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');
    }

    private function getExpenses(int $companyId, $start, $end): float
    {
        return (float) \App\Models\PurchaseOrder::where('company_id', $companyId)
            ->whereIn('status', ['approved', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');
    }

    private function getCustomerCount(int $companyId): int
    {
        return \App\Models\Customer::where('company_id', $companyId)->count();
    }

    private function getLeadCount(int $companyId): int
    {
        return \App\Models\Lead::where('company_id', $companyId)->count();
    }

    private function getLeadConversionRate(int $companyId): float
    {
        $total = \App\Models\Lead::where('company_id', $companyId)->count();
        if ($total === 0) {
            return 0.0;
        }
        $converted = \App\Models\Lead::where('company_id', $companyId)
            ->whereNotNull('converted_at')
            ->count();

        return round(($converted / $total) * 100, 2);
    }

    private function getProductCount(int $companyId): int
    {
        return \App\Models\Product::where('company_id', $companyId)->count();
    }

    private function getLowStockCount(int $companyId): int
    {
        return \App\Models\Stock::where('company_id', $companyId)
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->where('reorder_level', '>', 0)
            ->count();
    }

    private function getOpenOpportunityCount(int $companyId): int
    {
        return \App\Models\Opportunity::where('company_id', $companyId)
            ->where('status', 'open')
            ->count();
    }

    private function getOpenOpportunityValue(int $companyId): float
    {
        return (float) \App\Models\Opportunity::where('company_id', $companyId)
            ->where('status', 'open')
            ->sum('expected_value');
    }
}
