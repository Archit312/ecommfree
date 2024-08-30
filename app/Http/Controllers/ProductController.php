<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Admin;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PDO;

class ProductController extends Controller
{
    // Fetch all products
    public function getAllProducts(Request $request)
    {
        // Decode the incoming JSON payload
        $filters = $request->json()->all();

        // Start a query on the Product model
        $query = Product::query();

        // Filter by category_code if provided in the JSON
        if (isset($filters['category_code'])) {
            $query->where('product_category', $filters['category_code']);
        }

        // Filter by price range if provided in the JSON
        if (isset($filters['min_price']) && isset($filters['max_price'])) {
            $query->whereBetween('product_price', [$filters['min_price'], $filters['max_price']]);
        } elseif (isset($filters['min_price'])) {
            $query->where('product_price', '>=', $filters['min_price']);
        } elseif (isset($filters['max_price'])) {
            $query->where('product_price', '<=', $filters['max_price']);
        }

        // Execute the query and get all results (no pagination)
        $products = $query->get();

        // Return the response
        return response()->json(['products' => $products]);
    }

    // Insert a new product
    public function store(Request $request)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }

            // Validate request data
            $validatedData = $request->validate([
                'product_name' => 'required|string|max:255',
                'product_category' => 'required|string|max:255',
                'product_quantity' => 'required|string|max:255',
                'product_desc' => 'required|string|max:255',
                'product_price' => 'required|integer',
                'product_img_main' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
                'product_img_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
                'product_img_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
                'product_img_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
                'product_delivery_time' => 'required|integer',
            ]);

            // Generate unique product code
            $firstWord = strtok($request->product_name, " ");
            do {
                $productCode = $firstWord . rand(1000, 9999);
            } while (Product::where('product_code', $productCode)->exists());

            $validatedData['product_code'] = $productCode;

            // Handle main image upload
            if ($request->hasFile('product_img_main')) {
                $imageNameMain = $request->product_name . '_main_' . rand(1000, 9999) . '.' . $request->product_img_main->extension();
                $request->product_img_main->move(public_path('images'), $imageNameMain);
                $validatedData['product_img_main'] = 'images/' . $imageNameMain;
            }

            // Handle optional images upload
            if ($request->hasFile('product_img_1')) {
                $imageName1 = $request->product_name . '_1_' . rand(1000, 9999) . '.' . $request->product_img_1->extension();
                $request->product_img_1->move(public_path('images'), $imageName1);
                $validatedData['product_img_1'] = 'images/' . $imageName1;
            }

            if ($request->hasFile('product_img_2')) {
                $imageName2 = $request->product_name . '_2_' . rand(1000, 9999) . '.' . $request->product_img_2->extension();
                $request->product_img_2->move(public_path('images'), $imageName2);
                $validatedData['product_img_2'] = 'images/' . $imageName2;
            }

            if ($request->hasFile('product_img_3')) {
                $imageName3 = $request->product_name . '_3_' . rand(1000, 9999) . '.' . $request->product_img_3->extension();
                $request->product_img_3->move(public_path('images'), $imageName3);
                $validatedData['product_img_3'] = 'images/' . $imageName3;
            }

            // Create product
            $product_create = Product::create($validatedData);
            return response()->json($product_create, 201);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'product_code' => 'required|string|max:255'
            ]);

            $product_code = $request->input('product_code');
            Log::error('Product Code: ' . $product_code);

            $product = Product::where('product_code', $product_code)->first();

            if ($product != null) {
                Log::error('Product Details: ' . $product->toJson());
                return response()->json([
                    'success' => true,
                    'product' => $product
                ], 200);
            } else {
                return response()->json(['error' => 'product not found'], 404);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update an existing product using product_code
    public function update(Request $request, $product_code)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }
            $product = Product::where('product_code', $product_code)->firstOrFail();

            $validatedData = $request->validate([
                'product_name' => 'required|string|max:255',
                'product_category' => 'required|string|max:255',
                'product_quantity' => 'required|string|max:255',
                'product_desc' => 'required|string|max:255',
                'product_price' => 'required|integer',
                'product_img_main' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
                'product_img_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
                'product_img_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
                'product_img_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
                'product_delivery_time' => 'required|integer',
            ]);
            // Handle main image upload
            if ($request->hasFile('product_img_main')) {
                $imageNameMain = $request->product_name . '_main_' . rand(1000, 9999) . '.' . $request->product_img_main->extension();
                $request->product_img_main->move(public_path('images'), $imageNameMain);
                $validatedData['product_img_main'] = 'images/' . $imageNameMain;
            }

            // Handle optional images upload
            if ($request->hasFile('product_img_1')) {
                $imageName1 = $request->product_name . '_1_' . rand(1000, 9999) . '.' . $request->product_img_1->extension();
                $request->product_img_1->move(public_path('images'), $imageName1);
                $validatedData['product_img_1'] = 'images/' . $imageName1;
            }

            if ($request->hasFile('product_img_2')) {
                $imageName2 = $request->product_name . '_2_' . rand(1000, 9999) . '.' . $request->product_img_2->extension();
                $request->product_img_2->move(public_path('images'), $imageName2);
                $validatedData['product_img_2'] = 'images/' . $imageName2;
            }

            if ($request->hasFile('product_img_3')) {
                $imageName3 = $request->product_name . '_3_' . rand(1000, 9999) . '.' . $request->product_img_3->extension();
                $request->product_img_3->move(public_path('images'), $imageName3);
                $validatedData['product_img_3'] = 'images/' . $imageName3;
            }

            $product->update($validatedData);

            return response()->json($product, 200);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }

    // Search product
    public function search(Request $request)
    {
        $query = Product::query();

        if ($request->has('product_name')) {
            $query->where('product_name', 'LIKE', '%' . $request->input('product_name') . '%');
        }

        if ($request->has('product_category')) {
            $query->where('product_category', 'LIKE', '%' . $request->input('product_category') . '%');
        }

        if ($request->has('product_code')) {
            $query->where('product_code', 'LIKE', '%' . $request->input('product_code') . '%');
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('product_price', [$request->input('min_price'), $request->input('max_price')]);
        } elseif ($request->has('min_price')) {
            $query->where('product_price', '>=', $request->input('min_price'));
        } elseif ($request->has('max_price')) {
            $query->where('product_price', '<=', $request->input('max_price'));
        }

        if ($request->has('product_delivery_time')) {
            $query->where('product_delivery_time', $request->input('product_delivery_time'));
        }

        $products = $query->get();

        return response()->json(['products' => $products]);
    }

    // Delete a product using product_code
    public function destroy($product_code, Request $request)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }
            $product = Product::where('product_code', $product_code)->firstOrFail();
            $product->delete();

            return response()->json("Product Deleted Successfully", 204);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }
}
