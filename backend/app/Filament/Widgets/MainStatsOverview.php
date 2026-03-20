<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Medicine;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MainStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '15s'; // Auto-refresh every 15s

    protected function getStats(): array
    {
        $revenue = Order::where('payment_status', 'paid')->sum('total_amount');

        return [
            Stat::make('Total Revenue', number_format($revenue) . ' MMK')
                ->description('Consolidated Sales Performance')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart([7, 10, 5, 12, 18, 14, 25, 20, 28, 32])
                ->color('success'),

            Stat::make('Pending Orders', Order::where('status', 'pending')->count())
                ->description('Requires High-Priority Review')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([3, 5, 2, 8, 4, 3, 6])
                ->color('warning'),

            Stat::make('Approved Customers', Customer::where('status', 'approved')->count())
                ->description('Verified Medical Partners')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([15, 18, 22, 25, 28, 32, 35])
                ->color('primary'),

            Stat::make('Stock Criticality', $lowStockCount = Medicine::where('stock_quantity', '<=', 5)->count())
                ->description($lowStockCount > 0 ? 'Urgent Inventory Refill Needed' : 'Inventory Levels Optimized')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart([40, 35, 30, 25, 20, 15, 10, 5]) 
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}