<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopSaleResource\Pages;
use App\Models\Sale;
use App\Models\Medicine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShopSaleResource extends Resource
{
    protected static ?string $model = Sale::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Sales & Orders';
    protected static ?string $navigationLabel = 'Shop Sales';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->default(fn () => 'SALE/' . now()->format('Y/m/d') . '/' . strtoupper(substr(uniqid(), -4)))
                            ->required()
                            ->readOnly()
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer Name (Type here)')
                            ->placeholder('e.g. John Doe')
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'kbzpay' => 'KBZPay',
                            ])
                            ->default('cash')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Medicine Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('medicine_id')
                                    ->label('Medicine')
                                    ->options(Medicine::where('stock_quantity', '>', 0)->pluck('name', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                        $set('unit_price', Medicine::find($state)?->sell_price ?? 0)
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
                                $set('payable_amount', $total);
                            }),
                    ]),

                Forms\Components\Section::make('Payment Summary')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->readOnly()
                            ->prefix('MMK'),
                        Forms\Components\TextInput::make('discount')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => 
                                $set('payable_amount', $get('total_amount') - $state)
                            ),
                        Forms\Components\TextInput::make('payable_amount')
                            ->numeric()
                            ->readOnly()
                            ->prefix('MMK')
                            ->extraInputAttributes(['class' => 'font-bold text-lg text-success-600']),
                    ])->columns(3),

                Forms\Components\Section::make('Print Invoice')
                    ->visible(fn ($record) => $record !== null)
                    ->schema([
                        Forms\Components\Placeholder::make('print_instruction')
                            ->label('')
                            ->content('Click the button below to generate the customer receipt.'),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('print_now')
                                ->label('Print Customer Invoice')
                                ->icon('heroicon-o-printer')
                                ->color('info')
                                ->url(fn (Sale $record): string => route('invoice.print', $record))
                                ->openUrlInNewTab(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payable_amount')
                    ->money('MMK')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color(fn ($state) => $state === 'kbzpay' ? 'info' : 'success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Print Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (Sale $record): string => route('invoice.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShopSales::route('/'),
            'create' => Pages\CreateShopSale::route('/create'),
            'view' => Pages\ViewShopSale::route('/{record}'),
        ];
    }
}
