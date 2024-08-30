<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // Method for user signup
    public function signup(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'contact_no' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_no' => $request->contact_no,
        ]);


        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    // Method for user login
    public function login(Request $request)
    {
        // Validate the login credentials
        $credentials = $request->only('email', 'password');

        try {
            // Attempt to verify the credentials and create a token
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            // Something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // Get the authenticated user
        $user = auth()->user();

        // Return the token and user ID if successful
        return response()->json([
            'token' => $token,
            'user_id' => $user->id,
            'user'=>$user
        ]);
    }


    // Method for fetching user details
    public function show($id)
    {
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Remove the password from the user object before returning
        $user->makeHidden('password');

        return response()->json(['user' => $user], 200);
    }

    // Method for updating user data except the password
    public function update(Request $request, $id)
    {
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate the request data, including contact_no
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'contact_no' => 'sometimes|required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the user's data, excluding the password
        $user->name = $request->get('name', $user->name);
        $user->email = $request->get('email', $user->email);
        $user->contact_no = $request->get('contact_no', $user->contact_no);

        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    // Method for deleting a user
    public function destroy($id)
    {
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    // Method for user logout
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'User successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }

    // Method to get the authenticated user details
    public function me(Request $request)
    {
        return response()->json(auth()->user());
    }
}
