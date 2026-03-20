<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopMedicineChart extends ChartWidget
{
    protected static ?string $heading = 'Top Selling Medicines (by Volume)';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $data = DB::table('order_items')
            ->join('medicines', 'order_items.medicine_id', '=', 'medicines.id')
            ->select('medicines.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => $data->pluck('total_sold')->toArray(),
                    'backgroundColor' => [
                        '#10b981', // Emerald
                        '#3b82f6', // Blue
                        '#f59e0b', // Amber
                        '#ef4444', // Red
                        '#8b5cf6', // Violet
                    ],
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
