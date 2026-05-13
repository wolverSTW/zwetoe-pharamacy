<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('medicine_id')
                    ->relationship('medicine', 'name')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->prefix('MMK')
                    ->visible(fn () => auth()->user()->role === 'admin'),
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('MMK')
                    ->visible(fn () => auth()->user()->role === 'admin'),
            ]);
    }

    public function table(Table $table): Table
    {
        $isAdmin = auth()->user()->role === 'admin';

        return $table
            ->recordTitleAttribute('medicine.name')
            ->columns([
                Tables\Columns\ImageColumn::make('medicine.image')
                    ->label('')
                    ->circular(),
                Tables\Columns\TextColumn::make('medicine.name')
                    ->label('Medicine')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' MMK')
                    ->visible($isAdmin),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' MMK')
                    ->weight('black')
                    ->color('success')
                    ->visible($isAdmin),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Items are usually added via the storefront, so we disable direct adding here
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
