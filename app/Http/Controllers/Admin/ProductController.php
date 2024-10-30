<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerProduct;


class ProductController extends Controller
{
    //
    public function addproduct(){
        return view('admin.add-product');
    }

    public function createProduct(Request $request)
{
    $product = FlowerProduct::create([
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
        'category' => $request->category,
        'stock' => $request->stock,
        'duration' => $request->duration, // duration in months
    ]);

    // return response()->json(['message' => 'Product created successfully', 'product' => $product]);
    return redirect()->back()->with('success', 'Podcast created successfully.');

}

public function manageproduct()
{
    $products = FlowerProduct::where('status', 'active')->get();
    return view('admin.manage-product', compact('products'));
}

public function purchaseSubscription(Request $request)
{
    $product = FlowerProduct::findOrFail($request->product_id);

    $order = Order::create([
        'product_id' => $product->id,
        'user_id' => auth()->id(),
        'quantity' => 1,
        'total_price' => $product->price,
    ]);

    $startDate = now();
    $endDate = now()->addMonths($product->duration); // Calculate end date based on product duration

    Subscription::create([
        'user_id' => auth()->id(),
        'product_id' => $product->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'is_active' => true,
    ]);

    return response()->json(['message' => 'Subscription activated successfully', 'end_date' => $endDate]);
}

    public function deactivateExpiredSubscriptions()
{
    $subscriptions = Subscription::where('end_date', '<', now())
        ->where('is_active', true)
        ->get();

    foreach ($subscriptions as $subscription) {
        $subscription->update(['is_active' => false]);
    }

    return response()->json(['message' => 'Expired subscriptions deactivated']);
}

}
