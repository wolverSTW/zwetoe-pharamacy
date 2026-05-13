<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle New Customer Registration
     */
    public function register(Request $request)
    {
        // 1. Validate incoming request data
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:customers',
            'phone'                 => 'nullable|string|max:20',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        // 2. Create a new customer record with 'pending' status
        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'status'   => 'pending',
        ]);

        Cache::forget('admin_dashboard_stats');

        // 3. Send admin notifications after the response so registration returns faster.
        app()->terminating(function () use ($customer) {
            $receivers = User::whereIn('role', ['admin', 'staff'])->get();

            Notification::make()
                ->title('New Customer Registered')
                ->icon('heroicon-o-user-plus')
                ->body("**{$customer->name}** has joined the system.")
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url("/admin/customers"),
                ])
                ->sendToDatabase($receivers);
        });

        // 4. Return success response to Frontend
        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful! Please wait for admin approval.',
            'data' => $customer
        ], 201);
    }

    /**
     * Handle Customer Login
     */
    public function login(Request $request)
    {
        // 1. Validate login input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Check if the customer exists in the database
        $customer = Customer::where('email', $request->email)->first();

        // 3. Verify credentials (email and hashed password)
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password.'
            ], 401);
        }

        // 4. Only approved customers can log in.
        if ($customer->status !== 'approved') {
            $message = match ($customer->status) {
                'rejected' => 'Your account has been rejected. Please contact support.',
                default => 'Your account is pending admin approval.',
            };

            return response()->json([
                'status' => 'error',
                'message' => $message,
            ], 403);
        }

        // 5. Generate a new API token for the session
        $token = $customer->createToken('auth_token')->plainTextToken;

        // 6. Return success response with token and customer data
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'user' => $customer 
        ], 200);
    }

    /**
     * Handle User Logout
     */
    public function logout(Request $request)
    {
        // 1. Delete the current access token to revoke access (if it exists)
        if ($request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }
        
        // 2. Return logout confirmation
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }
}
