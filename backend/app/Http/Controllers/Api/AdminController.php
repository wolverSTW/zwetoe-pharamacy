<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get summary statistics for the Admin Dashboard
     */
    public function getDashboardStats()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_customers' => Customer::count(),
                'pending_approvals' => Customer::where('status', 'pending')->count(),
                'total_medicines' => Medicine::count(),
                'total_orders' => Order::count(),
            ]
        ]);
    }

    /**
     * List all customers with optional status filter
     */
    public function listCustomers(Request $request)
    {
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
        $request->validate([
            'status' => 'required|in:active,pending,suspended',
        ]);

        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->update([
            'status' => $request->status
        ]);

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
        $customer = Customer::find($id);
        
        if ($customer) {
            $customer->delete();
            return response()->json(['message' => 'Customer deleted successfully']);
        }

        return response()->json(['message' => 'Customer not found'], 404);
    }
}