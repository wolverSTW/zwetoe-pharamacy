<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue Trend';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    public ?string $filter = '7d';

    public static function canView(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected function getFilters(): ?array
    {
        return [
            '7d' => 'Last 7 Days',
            'week' => 'Weekly',
            'month' => 'Monthly',
            'year' => 'Yearly',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $data = match ($activeFilter) {
            'year' => collect(range(11, 0))->map(function ($months) {
                $date = Carbon::now()->subMonths($months);
                $total = Order::where('payment_status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_amount');
                return ['label' => $date->format('M Y'), 'total' => (float) $total];
            }),
            'month' => function() {
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $revenueData = Order::where('payment_status', 'paid')
                    ->where('created_at', '>=', $startDate)
                    ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                    ->groupBy('date')
                    ->pluck('total', 'date');
                return collect(range(29, 0))->map(function ($days) use ($revenueData) {
                    $date = Carbon::now()->subDays($days);
                    return [
                        'label' => $date->format('M d'),
                        'total' => (float) $revenueData->get($date->format('Y-m-d'), 0)
                    ];
                });
            },
            default => function() use ($activeFilter) {
                $daysCount = $activeFilter === 'week' ? 6 : 6;
                $startDate = Carbon::now()->subDays($daysCount)->startOfDay();
                $revenueData = Order::where('payment_status', 'paid')
                    ->where('created_at', '>=', $startDate)
                    ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                    ->groupBy('date')
                    ->pluck('total', 'date');
                return collect(range($daysCount, 0))->map(function ($days) use ($revenueData) {
                    $date = Carbon::now()->subDays($days);
                    return [
                        'label' => $date->format('M d'),
                        'total' => (float) $revenueData->get($date->format('Y-m-d'), 0)
                    ];
                });
            },
        };

        if (is_callable($data)) {
            $data = $data();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Revenue (MMK)',
                    'data'            => $data->pluck('total')->toArray(),
                    'fill'            => 'start',
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension'         => 0.4,
                    'pointRadius'     => 4,
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