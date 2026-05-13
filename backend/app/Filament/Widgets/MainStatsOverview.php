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
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        // ── Metrics ──
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $lastMonthRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('total_amount');
        $revenueTrendPercent = $lastMonthRevenue > 0 ? (($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        $totalOrders = Order::count();
        $pendingCustomers = Customer::where('status', 'pending')->count();
        $lowStockCount = Medicine::where('stock_quantity', '<=', 10)->count();

        // ── Revenue Chart (Last 7 days) ──
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        
        $revenueData = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $revenueTrend = collect(range(6, 0))->map(function ($d) use ($revenueData) {
            $date = Carbon::now()->subDays($d)->format('Y-m-d');
            return (float) $revenueData->get($date, 0);
        })->toArray();

        $stats = [];

        if ($isAdmin) {
            $stats[] = Stat::make('Total Sales', number_format($totalRevenue) . ' MMK')
                ->description($revenueTrendPercent >= 0 ? '↑ ' . number_format($revenueTrendPercent, 1) . '% growth' : '↓ ' . number_format(abs($revenueTrendPercent), 1) . '% drop')
                ->descriptionIcon($revenueTrendPercent >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($revenueTrend)
                ->color($revenueTrendPercent >= 0 ? 'success' : 'danger');
        }

        $stats[] = Stat::make('Total Orders', number_format($totalOrders))
            ->description('Lifetime volume')
            ->descriptionIcon('heroicon-m-shopping-cart')
            ->color('info');

        $stats[] = Stat::make('Pending Customers', number_format($pendingCustomers))
            ->description($pendingCustomers > 0 ? 'Requires approval' : 'All clear')
            ->descriptionIcon('heroicon-m-user-plus')
            ->color($pendingCustomers > 0 ? 'warning' : 'gray');

        $stats[] = Stat::make('Low Stock Inventory', number_format($lowStockCount))
            ->description($lowStockCount > 0 ? 'Restock needed' : 'Healthy inventory')
            ->descriptionIcon('heroicon-m-beaker')
            ->color($lowStockCount > 0 ? 'danger' : 'success');

        return $stats;
    }
}