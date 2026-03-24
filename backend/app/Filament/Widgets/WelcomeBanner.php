<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Order;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WelcomeBanner extends Widget
{
    protected static ?int $sort = -1;
    protected int | string | array $columnSpan = 'full';
    

    protected function getViewData(): array
    {
        /** @var User $user */
        $user = Auth::user();

        return [
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'lowStockCount' => Medicine::where('stock_quantity', '<', 10)->count(),
            'userName'      => $user ? $user->name : 'Admin',
        ];
    }

    protected static string $view = 'filament.widgets.welcome-banner';
}