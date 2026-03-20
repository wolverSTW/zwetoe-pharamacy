<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MedicineStats extends BaseWidget
{
    protected static ?int $sort = 99;
    protected static bool $isDiscovered = false; // Hidden — data merged into MainStatsOverview

    protected function getStats(): array
    {
        return [
            Stat::make('Total Medicines', Medicine::count())
                ->description('Active items in stock')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('success'),

            Stat::make('Low Stock Alert', Medicine::where('stock_quantity', '<', 10)->count())
                ->description('Needs restock soon')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Expiring Soon', Medicine::whereBetween('expiry_date', [now(), now()->addMonths(3)])->count())
                ->description('Next 3 months')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
        ];
    }
}