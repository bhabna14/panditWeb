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
        $products = FlowerProduct::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }
}
