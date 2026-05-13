<?php

namespace App\Filament\Resources\ShopSaleResource\Pages;

use App\Filament\Resources\ShopSaleResource;
use App\Models\Sale;
use Filament\Resources\Pages\ViewRecord;

class ViewShopSale extends ViewRecord
{
    protected static string $resource = ShopSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print')
                ->label('Print Invoice')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn (Sale $record): string => route('invoice.print', $record))
                ->openUrlInNewTab(),
        ];
    }
}
