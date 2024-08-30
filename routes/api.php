<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;

//User Api's
Route::post('/user/signup', [AuthController::class, 'signup']);
Route::put('/user/{id}', [AuthController::class, 'update'])->middleware('auth:api');
Route::get('/user/{id}', [AuthController::class, 'show'])->middleware('auth:api');
Route::delete('/user/{id}', [AuthController::class, 'destroy'])->middleware('auth:api');

Route::post('/login', [AuthController::class, 'login']);

//Products api
Route::get('/products', [ProductController::class, 'getAllProducts']);
Route::get('/products', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store'])->middleware('auth:api');
Route::put('/products/{product_code}', [ProductController::class, 'update'])->middleware('auth:api');
Route::delete('/products/{product_code}', [ProductController::class, 'destroy'])->middleware('auth:api');

//Category Api
Route::post('/categories', [CategoryController::class, 'store'])->middleware('auth:api');
Route::get('/categories', [CategoryController::class, 'index']);             // Create a new category
Route::get('/categories/{category_code}', [CategoryController::class, 'show']);  // Fetch category by code
Route::put('/categories/{category_code}', [CategoryController::class, 'update'])->middleware('auth:api'); // Update category by code
Route::delete('/categories/{category_code}', [CategoryController::class, 'destroy'])->middleware('auth:api'); // Delete category by code


Route::post('/admin/check', [AdminController::class, 'check'])->middleware('auth:api');
//Admin Api Route
Route::get('/admin', [AdminController::class, 'index'])->middleware('auth:api');          // Fetch all admins
Route::get('/admin/{id}', [AdminController::class, 'show'])->middleware('auth:api');      // Fetch a specific admin by ID
Route::post('/admin', [AdminController::class, 'store']);         // Store a new admin
Route::post('/admin/login', [AdminController::class, 'login']);         // Login a new admin
Route::put('/admin/{id}', [AdminController::class, 'update'])->middleware('auth:api');    // Update an existing admin by ID
Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->middleware('auth:api'); // Delete an admin by ID

//Carousels 
Route::get('/carousels', [CarouselController::class, 'index']);
Route::get('/carousels/{id}', [CarouselController::class, 'show']);
Route::post('/carousels', [CarouselController::class, 'store'])->middleware('auth:api');
Route::delete('/carousels/{id}', [CarouselController::class, 'destroy'])->middleware('auth:api');


Route::middleware('auth:api')->group(function () {
    //For Product Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
    //Get Cart Items Using User Id 
    route::get('/cart/items', [CartController::class, 'getCartItems']);
    //Add to Cart 
    route::post('/cart/add', [CartController::class, 'addToCart']);
});

//For Product Reviews Fetch
Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);