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
                ->description('Total consolidated sales')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart([7, 10, 5, 12, 18, 14, 25])
                ->color('success'),

            Stat::make('Pending Orders', Order::where('status', 'pending')->count())
                ->description('Requires immediate review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Active Customers', Customer::where('status', 'approved')->count())
                ->description('Verified accounts')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Low Stock Alert', $lowStockCount = Medicine::where('stock_quantity', '<=', 5)->count())
                ->description('Critical refill needed')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}