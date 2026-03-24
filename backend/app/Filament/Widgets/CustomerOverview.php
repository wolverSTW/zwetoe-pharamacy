<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
                ->description('Registered users')
                ->color('info'),

            Stat::make('New Requests', Customer::where('status', 'pending')->count())
                ->description('Waiting for approval')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
        ];
    }
}