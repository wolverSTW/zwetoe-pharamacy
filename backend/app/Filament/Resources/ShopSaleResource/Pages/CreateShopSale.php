<?php

namespace App\Filament\Resources\ShopSaleResource\Pages;

use App\Filament\Resources\ShopSaleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShopSale extends CreateRecord
{
    protected static string $resource = ShopSaleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
