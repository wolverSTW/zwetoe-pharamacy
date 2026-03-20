<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Weekly Revenue Trend';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = collect(range(6, 0))->map(function ($days) {
            $date = Carbon::now()->subDays($days);
            return [
                'label' => $date->format('D, M d'),
                'total' => Order::whereDate('created_at', $date)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (MMK)',
                    'data' => $data->pluck('total')->toArray(),
                    'fill' => 'start',
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}