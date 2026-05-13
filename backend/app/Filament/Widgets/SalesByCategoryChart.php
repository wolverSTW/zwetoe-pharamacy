<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Sales by Category';
    
    public static function canView(): bool
    {
        return auth()->user()->role === 'admin';
    }
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $data = DB::table('order_items')
            ->join('medicines', 'order_items.medicine_id', '=', 'medicines.id')
            ->join('categories', 'medicines.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.subtotal) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Revenue',
                    'data'            => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#059669', // Emerald
                        '#0284c7', // Blue
                        '#d97706', // Amber
                        '#e11d48', // Rose
                        '#7c3aed', // Violet
                        '#64748b', // Slate
                    ],
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ];
    }
}
