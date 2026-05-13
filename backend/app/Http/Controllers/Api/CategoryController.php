<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        // Categories rarely change — cache for 30 minutes
        $categories = Cache::remember('categories_all', now()->addMinutes(30), function () {
            return Category::select('id', 'name')->orderBy('name')->get();
        });

        return response()->json($categories)
            ->header('Cache-Control', 'public, max-age=1800');
    }
}
