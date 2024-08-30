<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);
    
        $review = Review::create([
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);
    
        return response()->json($review, 201);
    }
    
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)->get();
    
        return response()->json($reviews);
    }
    
}
