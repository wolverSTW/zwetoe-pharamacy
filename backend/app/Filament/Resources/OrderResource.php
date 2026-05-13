<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon  = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Sales & Orders';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function getNavigationBadge(): ?string
    {
        $count = Order::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return Order::where('status', 'pending')->count() > 0 ? 'warning' : 'primary';
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('invoice_number')
                        ->label('Invoice #')
                        ->disabled(),

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
                            'pending'    => 'Pending',
                            'processing' => 'Processing',
                            'completed'  => 'Completed',
                            'cancelled'  => 'Cancelled',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('payment_status')
                        ->label('Payment Status')
                        ->options([
                            'pending' => 'Pending',
                            'paid'    => 'Paid',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('payment_method')
                        ->label('Payment Method')
                        ->options([
                            'cash' => 'Cash',
                            'kpay' => 'K-Pay',
                            'wave' => 'Wave Money',
                            'card' => 'Card',
                        ])
                        ->default('cash')
                        ->required(),

                    Forms\Components\TextInput::make('total_amount')
                        ->label('Total Amount (MMK)')
                        ->numeric()
                        ->required()
                        ->readOnly(),
                ])->columns(3),

            Forms\Components\Section::make('Order Items')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('medicine_id')
                                ->label('Medicine')
                                ->relationship('medicine', 'name')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                    $set('unit_price', \App\Models\Medicine::find($state)?->sell_price ?? 0)
                                ),
                            Forms\Components\TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => 
                                    $set('subtotal', $state * $get('unit_price'))
                                ),
                            Forms\Components\TextInput::make('unit_price')
                                ->numeric()
                                ->prefix('MMK')
                                ->required()
                                ->readOnly(),
                            Forms\Components\TextInput::make('subtotal')
                                ->numeric()
                                ->prefix('MMK')
                                ->required()
                                ->readOnly(),
                        ])
                        ->columns(4)
                        ->live()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $total = collect($get('items'))->sum('subtotal');
                            $set('total_amount', $total);
                        }),
                ])->visible(true),

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


        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('General Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_number')
                            ->label('Invoice #')
                            ->fontFamily('mono')
                            ->size('sm')
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('id')->label('Order ID'),
                        Infolists\Components\TextEntry::make('customer.name')->label('Ordering Partner'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending'    => 'warning',
                                'processing' => 'info',
                                'completed'  => 'success',
                                'cancelled'  => 'danger',
                            }),
                        Infolists\Components\TextEntry::make('created_at')->dateTime()->label('Submission Date'),
                    ])->columns(5),



                Infolists\Components\Grid::make(2)
                    ->schema([
                        Infolists\Components\Section::make('Shipping Logistics')
                            ->schema([
                                Infolists\Components\TextEntry::make('shipping_method')->label('Logistics Mode'),
                                Infolists\Components\TextEntry::make('address')
                                    ->label('Final Destination')
                                    ->listWithLineBreaks(),
                            ]),

                        Infolists\Components\Section::make('Settlement Status')
                            ->schema([
                                Infolists\Components\TextEntry::make('payment_method')->label('Settlement Channel'),
                                Infolists\Components\TextEntry::make('payment_status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'danger',
                                        'paid'    => 'success',
                                    }),
                                Infolists\Components\TextEntry::make('total_amount')
                                    ->label('Total Consolidated Value')
                                    ->money('MMK')
                                    ->size('lg')
                                    ->weight('black'),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->color('gray')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->weight('semibold'),

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

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'danger',
                        'paid'    => 'success',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total (MMK)')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' MMK')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->summarize(Sum::make()->label('Grand Total')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('d-M-Y H:i')
                    ->sortable()
                    ->color('gray')
                    ->size('sm')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'completed'  => 'Completed',
                        'cancelled'  => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('From Date'),
                        DatePicker::make('created_until')->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'],
                                fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'],
                                fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->label('Update Status'),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit'  => Pages\EditOrder::route('/{record}/edit'),
            'view'  => Pages\ViewOrder::route('/{record}'),
        ];
    }
}