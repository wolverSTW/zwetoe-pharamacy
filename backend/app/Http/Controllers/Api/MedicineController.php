<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MedicineController extends Controller
{
    /**
     * Get a list of all medicines that are currently in stock.
     */
    public function index(Request $request)
    {
        // Build a cache key that varies by category filter
        $cacheKey = 'medicines_list_' . ($request->get('category_id', 'all'));

        $medicines = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            $query = Medicine::with('category:id,name')
                ->select('id', 'category_id', 'name', 'generic_name', 'sell_price', 'stock_quantity', 'image')
                ->where('stock_quantity', '>', 0)
                ->where('is_active', true);

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            return $query->orderBy('name')->get();
        });

        return response()->json([
            'status' => 'success',
            'count'  => $medicines->count(),
            'data'   => $medicines
        ], 200)->header('Cache-Control', 'public, max-age=300');
    }

    /**
     * Get details of a single medicine with its category.
     */
    public function show($id)
    {
        // Load medicine with its category for the detail page
        $medicine = Medicine::with('category')->find($id);

        if (!$medicine) {
            return response()->json([
                'status' => 'error',
                'message' => 'Medicine not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $medicine
        ], 200);
    }

    /**
     * Search medicines by name or description.
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');

        if (strlen($term) < 2) {
            return response()->json(['status' => 'success', 'data' => []]);
        }

        // Cache search results for 2 minutes per unique query
        $medicines = Cache::remember('search_' . md5($term), now()->addMinutes(2), function () use ($term) {
            return Medicine::with('category:id,name')
                ->select('id', 'category_id', 'name', 'generic_name', 'sell_price', 'stock_quantity', 'image')
                ->where(function ($q) use ($term) {
                    $q->where('name', 'LIKE', "%{$term}%")
                      ->orWhere('generic_name', 'LIKE', "%{$term}%");
                })
                ->where('is_active', true)
                ->orderBy('name')
                ->limit(30)
                ->get();
        });

        return response()->json([
            'status' => 'success',
            'data'   => $medicines
        ], 200);
    }
}