<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Sales';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('id')
                        ->label('Order ID')
                        ->disabled(),

                    Forms\Components\Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->label('Customer Name')
                        ->disabled(), 

                    Forms\Components\Select::make('status')
                        ->label('Order Status')
                        ->options([
                            'pending' => 'Pending',
                            'processing' => 'Processing',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('payment_status')
                        ->label('Payment Status')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\TextInput::make('payment_method')
                        ->label('Payment Method')
                        ->disabled(),

                    Forms\Components\TextInput::make('total_amount')
                        ->label('Total Amount (MMK)')
                        ->numeric()
                        ->disabled(),
                ])->columns(2),
            
            Forms\Components\Section::make('Payment Verification')
                ->schema([
                    Forms\Components\FileUpload::make('payment_screenshot')
                        ->label('Payment Receipt')
                        ->image()
                        ->directory('payment-proofs')
                        ->visibility('public')
                        ->openable() 
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Shipping Details')
                ->schema([
                    Forms\Components\TextInput::make('shipping_method')
                        ->label('Shipping Method')
                        ->disabled(),

                    Forms\Components\KeyValue::make('address')
                        ->label('Delivery Address')
                        ->visible(fn ($record) => $record?->shipping_method === 'delivery')
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Ordered Medicines')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('medicine_id')
                                ->relationship('medicine', 'name')
                                ->label('Medicine')
                                ->disabled(),

                            Forms\Components\TextInput::make('quantity')
                                ->numeric()
                                ->disabled(),

                            Forms\Components\TextInput::make('unit_price')
                                ->label('Unit Price')
                                ->numeric()
                                ->prefix('MMK')
                                ->disabled(),

                            Forms\Components\TextInput::make('subtotal')
                                ->numeric()
                                ->prefix('MMK')
                                ->disabled(),
                        ])
                        ->columns(4)
                        ->disabled()
                        ->deletable(false) 
                        ->addable(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('payment_screenshot')
                    ->label('Receipt')
                    ->boolean()
                    ->trueIcon('heroicon-o-camera')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method'),

                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'danger',
                        'paid' => 'success',
                    }),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total (MMK)')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total Revenue')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('d-M-Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('From Date'),
                        DatePicker::make('created_until')->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->defaultSort('created_at', 'desc') 
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Update Status'),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}