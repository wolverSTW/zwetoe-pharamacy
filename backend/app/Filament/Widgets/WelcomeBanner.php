<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Medicine;
use App\Models\Customer;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeBanner extends Widget
{
    protected static ?int $sort = -1;
    protected static ?string $pollingInterval = '60s';
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        /** @var User $user */
        $user = Auth::user();

        $todayRevenue   = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $totalOrders    = Order::count();
        $pendingOrders  = Order::where('status', 'pending')->count();
        $lowStockCount  = Medicine::where('stock_quantity', '<', 10)->count();
        $pendingCustomers = Customer::where('status', 'pending')->count();

        return [
            'pendingOrders'    => $pendingOrders,
            'lowStockCount'    => $lowStockCount,
            'todayRevenue'     => $todayRevenue,
            'totalOrders'      => $totalOrders,
            'pendingCustomers' => $pendingCustomers,
            'userName'         => $user ? $user->name : 'Admin',
            'lastUpdate'       => now()->format('h:i A'),
        ];
    }

    protected static string $view = 'filament.widgets.welcome-banner';
}