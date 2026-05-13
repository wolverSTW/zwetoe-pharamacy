<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    private function checkAdmin()
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Get summary statistics for the Admin Dashboard
     */
    public function getDashboardStats()
    {
        $this->checkAdmin();

        $stats = Cache::remember('admin_dashboard_stats', now()->addSeconds(60), function () {
            return [
                'total_customers' => Customer::count(),
                'pending_approvals' => Customer::where('status', 'pending')->count(),
                'total_medicines' => Medicine::count(),
                'total_orders' => Order::count(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    /**
     * List all customers with optional status filter
     */
    public function listCustomers(Request $request)
    {
        $this->checkAdmin();
        $status = $request->query('status'); // e.g., ?status=pending
        
        $query = Customer::latest();
        
        if ($status) {
            $query->where('status', $status);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()
        ]);
    }

    /**
     * Approve or Update Customer Status
     */
    public function updateCustomerStatus(Request $request, $id)
    {
        $this->checkAdmin();
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->update([
            'status' => $request->status
        ]);

        Cache::forget('admin_dashboard_stats');

        return response()->json([
            'status' => 'success',
            'message' => "Customer status updated to {$request->status}",
            'data' => $customer
        ]);
    }

    /**
     * Delete a customer record
     */
    public function deleteCustomer($id)
    {
        $this->checkAdmin();
        $customer = Customer::find($id);
        
        if ($customer) {
            $customer->delete();
            Cache::forget('admin_dashboard_stats');
            return response()->json(['message' => 'Customer deleted successfully']);
        }

        return response()->json(['message' => 'Customer not found'], 404);
    }
}
