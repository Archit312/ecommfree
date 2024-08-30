<?php
// app/Http/Controllers/CarouselController.php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Carousel;
use Exception;
use Illuminate\Support\Facades\Storage;

class CarouselController extends Controller
{
    // Store an image
    public function store(Request $request)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }

            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:5120', // 5MB in kilobytes
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $destinationPath = public_path('carousels');
                $file->move($destinationPath, $fileName);

                // Store the file path in the database
                $carousel = new Carousel();
                $carousel->image_path = 'carousels/' . $fileName; // Relative path for storage
                $carousel->save();

                return response()->json(['message' => 'Image uploaded successfully!', 'data' => $carousel], 201);
            }

            return response()->json(['message' => 'No image file provided.'], 400);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }

    // Fetch all images
    public function index()
    {
        $carousels = Carousel::all();
        return response()->json(['data' => $carousels], 200);
    }

    // Fetch a specific image by ID
    public function show($id)
    {
        $carousel = Carousel::find($id);

        if ($carousel) {
            return response()->json(['data' => $carousel], 200);
        }

        return response()->json(['message' => 'Image not found.'], 404);
    }

    // Delete an image
    public function destroy($id,Request $request)
    {
        try {
            // Check if user_id exists in the admin table
            $user_id = $request->input('user_id');
            if (!Admin::where('user_id', $user_id)->exists()) {
                return response()->json(['error' => 'Unauthorized user'], 403);
            }

            $carousel = Carousel::find($id);

            if ($carousel) {
                // Delete the file from storage
                $filePath = public_path($carousel->image_path);
                if (file_exists($filePath)) {
                    unlink($filePath); // Remove the file
                }
                // Delete the record from the database
                $carousel->delete();

                return response()->json(['message' => 'Image deleted successfully.'], 200);
            }

            return response()->json(['message' => 'Image not found.'], 404);
        } catch (Exception $e) {
            // Return a generic error message in case of unexpected exceptions
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }
}
