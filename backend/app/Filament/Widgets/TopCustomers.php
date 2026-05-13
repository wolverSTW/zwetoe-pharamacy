<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopCustomers extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 1;
    protected function getHeading(): ?string { return 'Top 10 Customers'; }

    public static function canView(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()
                    ->where('status', 'approved')
                    ->orderByDesc('total_spent')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Spent')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' MMK')
                    ->color('success')
                    ->weight('black'),
                Tables\Columns\TextColumn::make('phone')
                    ->size('xs')
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
