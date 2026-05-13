<?php

namespace App\Filament\Resources\ShopSaleResource\Pages;

use App\Filament\Resources\ShopSaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShopSales extends ListRecords
{
    protected static string $resource = ShopSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Record New Sale'),
        ];
    }
}
