<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected function getHeading(): ?string { return 'Recent Orders'; }

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(8))
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->fontFamily('mono')
                    ->color('gray')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' MMK')
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\IconColumn::make('payment_screenshot')
                    ->label('Receipt')
                    ->boolean()
                    ->trueIcon('heroicon-o-camera')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('info')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'processing' => 'info',
                        'completed'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending'    => 'heroicon-m-clock',
                        'processing' => 'heroicon-m-arrow-path',
                        'completed'  => 'heroicon-m-check-circle',
                        'cancelled'  => 'heroicon-m-x-circle',
                        default      => 'heroicon-m-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'danger',
                        'paid'    => 'success',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, H:i')
                    ->color('gray')
                    ->size('sm'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Order $record): string => "/admin/orders/{$record->id}")
                    ->icon('heroicon-m-eye')
                    ->iconButton()
                    ->color('primary'),
            ])
            ->paginated(false);
    }
}