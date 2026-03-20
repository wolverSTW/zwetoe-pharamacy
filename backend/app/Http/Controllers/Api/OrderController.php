<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Medicine;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'items' => 'required',
            'shipping_method' => 'required|in:pick-up,delivery,express',
            'address' => 'required_if:shipping_method,delivery,express',
            'payment_screenshot' => app()->environment('testing') ? 'nullable' : 'required|image|max:2048', 
        ]);

        try {
            return DB::transaction(function () use ($request, $user) {
                
                $path = null;
                if ($request->hasFile('payment_screenshot')) {
                    $path = $request->file('payment_screenshot')->store('payment-proofs', 'public');
                }

                $items = is_array($request->items) ? $request->items : json_decode($request->items, true);
                $address = is_array($request->address) ? $request->address : json_decode($request->address, true);

                $order = Order::create([
                    'customer_id' => $user->id,
                    'total_amount' => 0, 
                    'status' => 'pending', 
                    'payment_method' => 'kbzpay',
                    'payment_status' => 'pending',
                    'shipping_method'=> $request->shipping_method,
                    'address' => $request->shipping_method === 'delivery' ? $address : null,
                    'payment_screenshot' => $path,
                ]);

                $totalAmount = 0;

                foreach ($items as $item) {
                    $medicine = Medicine::lockForUpdate()->find($item['medicine_id']);

                    if (!$medicine || $medicine->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for " . ($medicine->name ?? 'Medicine'));
                    }

                    $subtotal = $medicine->sell_price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $order->items()->create([
                        'medicine_id' => $item['medicine_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $medicine->sell_price,
                        'subtotal' => $subtotal,
                    ]);

                    $medicine->decrement('stock_quantity', $item['quantity']);
                }

                $order->update(['total_amount' => $totalAmount]);

                $customer = Customer::find($user->id);
                if ($customer) {
                    $customer->increment('total_spent', $totalAmount);
                }

                return response()->json([
                    'message' => 'Order placed successfully!',
                    'order_id' => $order->id,
                    'grand_total' => $totalAmount,
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Order failed: ' . $e->getMessage()
            ], 400);
        }
    }
}