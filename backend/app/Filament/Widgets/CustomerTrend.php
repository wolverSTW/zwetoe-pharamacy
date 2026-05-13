<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class CustomerTrend extends ChartWidget
{
    protected static ?string $heading = 'Customer Growth Trend';
    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return auth()->user()->role === 'admin';
    }
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $customerData = Customer::where('created_at', '>=', $startDate)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(id) as total')
            ->groupBy('year', 'month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [sprintf('%04d-%02d', $item->year, $item->month) => $item->total];
            });

        $data = collect(range(6, 0))->map(function ($months) use ($customerData) {
            $date = Carbon::now()->subMonths($months);
            $key = $date->format('Y-m');
            return [
                'label' => $date->format('M Y'),
                'total' => $customerData->get($key, 0),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'fill' => true,
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
