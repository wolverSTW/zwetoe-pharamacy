<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = '14-Day Revenue Trend';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '220px';

    protected function getData(): array
    {
        $data = collect(range(13, 0))->map(function ($days) {
            $date = Carbon::now()->subDays($days);
            return [
                'label' => $date->format('M d'),
                'total' => Order::whereDate('created_at', $date)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
            ];
        });

        return [
            'datasets' => [
                [
                    'label'           => 'Revenue (MMK)',
                    'data'            => $data->pluck('total')->toArray(),
                    'fill'            => 'start',
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.08)',
                    'tension'         => 0.4,
                    'pointBackgroundColor' => '#10b981',
                    'pointRadius'          => 4,
                    'pointHoverRadius'     => 6,
                    'borderWidth'          => 2,
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.08)'],
                    'ticks' => ['color' => '#64748b'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['color' => '#64748b'],
                ],
            ],
        ];
    }
}