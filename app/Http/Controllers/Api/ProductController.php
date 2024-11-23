<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerProduct;
class ProductController extends Controller
{
    //

    public function getActiveProducts()
{
    $products = FlowerProduct::where('status', 'active')
    ->get()->map(function ($product) {
        // Append full URL to product image
        $product->product_image = $product->product_image 
            ? asset('storage/' . $product->product_image) 
            : null;
        return $product;
    });

    return response()->json([
        'status' => 200,
        'message' => 'Products retrieved successfully.',
        'data' => $products
    ], 200);
}

}
