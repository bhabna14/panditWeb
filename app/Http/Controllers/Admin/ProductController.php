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
        // Validate inputs, including image validation
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'category' => 'required|string',
            // 'stock' => 'required|integer',
            // 'duration' => 'required|integer',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000', // image validation
        ]);
    
        // Handle the image upload
        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('product_images', 'public'); // store in 'public/product_images' folder
        }
        $productId = 'FLOW' . mt_rand(1000000, 9999999);
        // Create the product with image path
        $product = FlowerProduct::create([
            'product_id' => $productId,
            'name' => $request->name,
            'price' => $request->price,
            'mrp' => $request->mrp,

            'description' => $request->description,
            'category' => $request->category,
            'stock' => $request->stock,
            'duration' => $request->duration,
            'product_image' => $imagePath ?? null, // save the image path if it exists
        ]);
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Product created successfully.');
    }
    public function editProduct($id)
    {
        $product = FlowerProduct::findOrFail($id); // Fetch the product details
        return view('admin.edit-product', compact('product'));
    }    
    public function updateProduct(Request $request, $id)
    {
        $product = FlowerProduct::findOrFail($id);
    
        // Validate inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'category' => 'required|string',
            // 'stock' => '|integer',
            // 'duration' => 'required|integer',
            'product_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Update product details
        $product->update([
            'name' => $request->name,
            'mrp' => $request->mrp,

            'price' => $request->price,
            'description' => $request->description,
            'category' => $request->category,
            'stock' => $request->stock,
            'duration' => $request->duration,
        ]);
    
        // Handle image upload if a new image is provided
        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('product_images', 'public');
            $product->update(['product_image' => $imagePath]);
        }
    
        return redirect()->route('manageproduct')->with('success', 'Product updated successfully.');
    }
    public function deleteProduct($id)
    {
        $product = FlowerProduct::findOrFail($id);
        // dd($product);

        $product->update(['status' => 'deleted']);
    
        return redirect()->route('manageproduct')->with('success', 'Product deleted successfully.');
    }
        
public function manageproduct()
{
    $products = FlowerProduct::where('status', 'active')->get();
    return view('admin.manage-product', compact('products'));
}




}
