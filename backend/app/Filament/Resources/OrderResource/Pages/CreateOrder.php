<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Medicine;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * Triggered after the Order record is successfully created in the database.
     */
    protected function afterCreate(): void
    {
        $order = $this->record;

        // Use a database transaction to ensure data integrity
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                // 1. Deduct stock quantity from Medicine table
                $medicine = Medicine::find($item->medicine_id);
                if ($medicine) {
                    $medicine->decrement('stock_quantity', $item->quantity);
                }
            }

            // 2. Increment the total amount spent by the customer
            $customer = Customer::find($order->customer_id);
            if ($customer) {
                $customer->increment('total_spent', $order->total_amount);
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}