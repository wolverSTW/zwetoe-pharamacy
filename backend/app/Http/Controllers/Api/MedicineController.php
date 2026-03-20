<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    /**
     * Get a list of all medicines that are currently in stock.
     */
    public function index(Request $request)
    {
        // 1. Fetch medicines with their category information (Eager Loading)
        // 2. Filter only items in stock
        $query = Medicine::with('category') 
            ->where('stock_quantity', '>', 0);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $medicines = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'count' => $medicines->count(),
            'data' => $medicines
        ], 200);
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
        $query = $request->get('q');
        
        $medicines = Medicine::with('category')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $medicines
        ], 200);
    }
}