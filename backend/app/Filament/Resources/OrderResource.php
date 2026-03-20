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
        return false;
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

                Infolists\Components\Section::make('Inventory Selection (Items)')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\ImageEntry::make('medicine.image')
                                    ->label('Visual Ref')
                                    ->visibility('public')
                                    ->disk('public'),
                                Infolists\Components\TextEntry::make('medicine.name')
                                    ->label('Medicine Name')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Units'),
                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label('Unit Val')
                                    ->money('MMK'),
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label('Line Total')
                                    ->money('MMK')
                                    ->color('success')
                                    ->weight('black'),
                            ])->columns(5)
                    ]),

                Infolists\Components\Grid::make(2)
                    ->schema([
                        Infolists\Components\Section::make('Shipping Logistics')
                            ->schema([
                                Infolists\Components\TextEntry::make('shipping_method')->label('Logistics Mode'),
                                Infolists\Components\TextEntry::make('address')
                                    ->label('Final Destination')
                                    ->listWithLineBreaks()
                                    ->limitObjects(5),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit'  => Pages\EditOrder::route('/{record}/edit'),
            'view'  => Pages\ViewOrder::route('/{record}'),
        ];
    }
}