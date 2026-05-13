<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class ExpiredMedicineList extends BaseWidget
{
    protected static ?int $sort = 9;
    protected int | string | array $columnSpan = 1;
    protected function getHeading(): ?string { return 'Expired / Expiring Soon'; }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Medicine::query()
                    ->where('expiry_date', '<=', Carbon::now()->addMonths(3))
                    ->orderBy('expiry_date', 'asc')
                    ->limit(6)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('semibold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiry')
                    ->date('M d, Y')
                    ->color(fn ($state) => Carbon::parse($state)->isPast() ? 'danger' : 'warning')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->numeric()
                    ->alignRight(),
            ])
            ->paginated(false);
    }
}
