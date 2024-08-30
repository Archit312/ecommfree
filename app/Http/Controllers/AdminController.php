<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Fetch all admin records
    public function index(Request $request)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }
            $admins = Admin::all();
            return response()->json($admins, 200);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }

    // Fetch a specific admin by ID
    public function show($id,Request $request)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }
            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json(['message' => 'Admin not found'], 404);
            }

            return response()->json($admin, 200);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }

    // Store a new admin
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'additional_email' => 'nullable|email',
            'terms_and_conditions' => 'nullable|string',
            'about_us' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $admin = new Admin([
            'user_id' => $request->input('user_id'),
            'additional_email' => $request->input('additional_email'),
            'terms_and_conditions' => $request->input('terms_and_conditions'),
            'about_us' => $request->input('about_us'),
        ]);

        $admin->save();

        return response()->json(['message' => 'Admin created successfully!', 'data' => $admin], 201);
    }

    // Update an existing admin by ID
    public function update(Request $request, $id)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }
            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json(['message' => 'Admin not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'admin_email' => 'sometimes|required|email|unique:admin,admin_email,' . $id,
                'admin_password' => 'sometimes|required|min:6',
                'additional_email' => 'nullable|email',
                'terms_and_conditions' => 'nullable|string',
                'about_us' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $admin->admin_email = $request->input('admin_email', $admin->admin_email);
            $admin->additional_email = $request->input('additional_email', $admin->additional_email);
            $admin->terms_and_conditions = $request->input('terms_and_conditions', $admin->terms_and_conditions);
            $admin->about_us = $request->input('about_us', $admin->about_us);

            if ($request->has('admin_password')) {
                $admin->admin_password = Hash::make($request->input('admin_password'));
            }

            $admin->save();

            return response()->json(['message' => 'Admin updated successfully!', 'data' => $admin], 200);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }

    // Delete an admin by ID
    public function destroy($id,Request $request)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }
            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json(['message' => 'Admin not found'], 404);
            }

            $admin->delete();

            return response()->json(['message' => 'Admin deleted successfully!'], 200);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }

    public function check(Request $request)
    {
        // Validate the request to ensure 'user_id' is present and is an integer
        $request->validate([
            'user_id' => 'required|integer'
        ]);

        // Extract user_id from the request
        $userId = $request->input('user_id');

        // Check if the provided user_id exists in the admin table
        $exists = Admin::where('user_id', $userId)->exists();

        // Return the appropriate JSON response
        if ($exists) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'User ID not found in admin table'], 404);
        }
    }
}
