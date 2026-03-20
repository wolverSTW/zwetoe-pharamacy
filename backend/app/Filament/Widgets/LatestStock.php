<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use App\Filament\Resources\MedicineResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestStock extends BaseWidget
{
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 4;
    protected function getHeading(): ?string { return '⚠️ Critical Stock — Refill Needed'; }

    public function table(Table $table): Table
    {
        return $table
            ->query(Medicine::query()->orderBy('stock_quantity', 'asc')->limit(8))
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(36),

                Tables\Columns\TextColumn::make('name')
                    ->weight('semibold')
                    ->description(fn (Medicine $record): string => $record->generic_name ?? ''),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0  => 'danger',
                        $state <= 5  => 'danger',
                        $state <= 15 => 'warning',
                        default      => 'success',
                    })
                    ->formatStateUsing(fn (int $state): string => $state <= 0 ? 'OUT' : $state . ' left'),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->date('M d, Y')
                    ->color(fn (?string $state): string => $state && \Carbon\Carbon::parse($state)->isPast()
                        ? 'danger'
                        : (\Carbon\Carbon::parse($state)->diffInDays(now()) < 90 ? 'warning' : 'gray')
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('Restock')
                    ->url(fn (Medicine $record) => MedicineResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-arrow-path')
                    ->button()
                    ->size('sm')
                    ->color('primary'),
            ])
            ->paginated(false);
    }
}