<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use App\Filament\Resources\MedicineResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestStock extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;
    protected function getHeading(): ?string { return 'Critical Stock Level (Refill Needed)'; }

    public function table(Table $table): Table
    {
        return $table
            ->query(Medicine::query()->orderBy('stock_quantity', 'asc')->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')->weight('bold'),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Remaining')
                    ->color(fn ($state) => $state <= 5 ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('expiry_date')->date(),
            ])
            ->actions([
                Tables\Actions\Action::make('Restock')
                    ->url(fn (Medicine $record) => MedicineResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-arrow-path')
                    ->button(),
            ])
            ->paginated(false);
    }
}