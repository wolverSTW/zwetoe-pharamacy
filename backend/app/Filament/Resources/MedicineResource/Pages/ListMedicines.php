<?php

namespace App\Filament\Resources\MedicineResource\Pages;

use App\Filament\Resources\MedicineResource;
use App\Models\Medicine;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMedicines extends ListRecords
{
    protected static string $resource = MedicineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // Adding Tabs for Quick Filtering
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Medicines'),
            
            'low_stock' => Tab::make('Low Stock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('stock_quantity', '<', 10))
                ->badge(Medicine::where('stock_quantity', '<', 10)->count())
                ->badgeColor('danger'),

            'expiring' => Tab::make('Expiring Soon')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('expiry_date', '<=', now()->addDays(30)))
                ->badge(Medicine::where('expiry_date', '<=', now()->addDays(30))->count())
                ->badgeColor('warning'),

            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('expiry_date', '<', now()))
                ->badge(Medicine::where('expiry_date', '<', now())->count())
                ->badgeColor('danger'),
        ];
    }
}