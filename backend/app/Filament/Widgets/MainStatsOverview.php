<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Medicine;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class MainStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $todayRevenue = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalOrders   = Order::count();
        $approvedCustomers = Customer::where('status', 'approved')->count();
        $pendingCustomers  = Customer::where('status', 'pending')->count();
        $lowStockCount  = Medicine::where('stock_quantity', '<=', 5)->count();
        $expiringSoon   = Medicine::whereBetween('expiry_date', [now(), now()->addMonths(1)])->count();

        // 7-day revenue trend for chart sparkline
        $revenueTrend = collect(range(6, 0))->map(fn ($d) =>
            Order::whereDate('created_at', Carbon::now()->subDays($d))
                ->where('payment_status', 'paid')
                ->sum('total_amount')
        )->toArray();

        // 7-day pending orders trend
        $pendingTrend = collect(range(6, 0))->map(fn ($d) =>
            Order::whereDate('created_at', Carbon::now()->subDays($d))
                ->where('status', 'pending')
                ->count()
        )->toArray();

        return [
            Stat::make('Total Revenue', number_format($totalRevenue) . ' MMK')
                ->description(
                    'Today: ' . number_format($todayRevenue) . ' MMK · ' .
                    $totalOrders . ' total orders'
                )
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($revenueTrend)
                ->color('success'),

            Stat::make('Pending Orders', $pendingOrders)
                ->description('Awaiting fulfillment — action required')
                ->descriptionIcon('heroicon-m-clock')
                ->chart($pendingTrend)
                ->color($pendingOrders > 10 ? 'danger' : ($pendingOrders > 0 ? 'warning' : 'success')),

            Stat::make('Active Customers', $approvedCustomers)
                ->description($pendingCustomers . ' awaiting verification')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([15, 18, 22, 25, 28, 32, $approvedCustomers])
                ->color('info'),

            Stat::make('Stock Alerts', $lowStockCount)
                ->description(
                    $lowStockCount > 0 ? 'Critical: refill immediately' : 'Inventory healthy' .
                    ($expiringSoon > 0 ? " · {$expiringSoon} expiring" : '')
                )
                ->descriptionIcon($lowStockCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->chart([40, 35, 30, 25, 20, 15, max(0, $lowStockCount)])
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}