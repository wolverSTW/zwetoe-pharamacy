<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerAuthController extends Controller
{
    /**
     * Customer Registration API
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:customers',
            'phone' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
            'region' => 'nullable|string',
            'township' => 'nullable|string',
            'town' => 'nullable|string',
            'street' => 'nullable|string',
            'house_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'avatar_url' => $request->avatar_url ?? null,
            'region' => $request->region,
            'township' => $request->township,
            'town' => $request->town,
            'street' => $request->street,
            'house_number' => $request->house_number,
            'status' => 'active', 
            'total_spent' => 0,
        ]);

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Customer registered successfully',
            'token' => $token,
            'data' => $customer
        ], 201);
    }

    /**
     * Customer Login API
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($customer->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is ' . $customer->status . '. Reason: ' . $customer->reject_reason
            ], 403);
        }

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'data' => $customer
        ], 200);
    }

    /**
     * Get Authenticated Customer Profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }

    /**
     * Update Customer Profile (For Registered Customers)
     */
    public function updateProfile(Request $request)
    {
        $customer = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'region' => 'nullable|string',
            'township' => 'nullable|string',
            'town' => 'nullable|string',
            'street' => 'nullable|string',
            'house_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $customer->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => $customer
        ]);
    }

    /**
     * Customer Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }
}