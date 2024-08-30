<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|string',
                'product_code' => 'required|string|exists:products,product_code',
                'quantity' => 'required|integer|min:1',
            ]);

            $cartItem = Cart::updateOrCreate(
                ['user_id' => $validated['user_id'], 'product_code' => $validated['product_code']],
                ['quantity' => DB::raw("quantity + {$validated['quantity']}")]
            );

            return response()->json(['message' => 'Product added to cart successfully', 'cart' => $cartItem], 200);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error adding to cart: ' . $e->getMessage());

            // Return a generic error response
            return response()->json(['error' => 'An error occurred while adding to cart.'], 500);
        }
    }
    public function getCartItems(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|string',
            ]);

            $cartItems = Cart::with('product')
                ->where('user_id', $validated['user_id'])
                ->get();

            return response()->json(['cart_items' => $cartItems], 200);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error fetching cart items: ' . $e->getMessage());

            // Return a generic error response
            return response()->json(['error' => 'An error occurred while fetching cart items.'], 500);
        }
    }
}
