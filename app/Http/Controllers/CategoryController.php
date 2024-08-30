<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Category;
use Exception;

class CategoryController extends Controller
{

    // Store a new category
    public function store(Request $request)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }
            $request->validate([
                'category_name' => 'required|string|max:255',
            ]);

            $category_name = $request->input('category_name');
            $category_code = $category_name . '-' . rand(1000, 9999);

            $category = Category::create([
                'category_code' => $category_code,
                'category_name' => $category_name,
            ]);

            return response()->json($category, 201);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }
    //Fetch All Category 
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories, 200);
    }

    // Fetch a category by category_code
    public function show($category_code)
    {

        // Check if the provided catehory code exists in the category table
        $exist = Category::where('category_code', $category_code)->first();

        // Return the appropriate JSON response
        if ($exist) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Category Not Found'], 404);
        }
    }

    // Update a category by category_code
    public function update(Request $request, $category_code)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }
            $request->validate([
                'category_name' => 'sometimes|string|max:255',
            ]);

            $category = Category::where('category_code', $category_code)->first();

            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            $category->update($request->only('category_name'));

            return response()->json($category, 200);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }

    // Delete a category by category_code
    public function destroy($category_code,Request $request)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }
            $category = Category::where('category_code', $category_code)->first();

            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            $category->delete();

            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }
}
