<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use Illuminate\Support\Facades\Storage;
class PodcastController extends Controller
{
    //
    public function managepodcast(){
        $podcasts = Podcast::all();
        return view('admin/managepodcast',compact('podcasts'));
    }
    public function addpodcast(){

        $podcasts = Podcast::all();
        $categories = PodcastCategory::where('status', 'active')->get();
        return view('admin/addpodcast',compact('podcasts','categories'));
    }
    public function savepodcast(Request $request)
{
    // Validate the request data
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        'music' => 'required|mimes:mp3,wav|max:30000', // Added validation for music file size
        'podcast_id' => 'nullable|string', // Ensure validation is set for podcast_id
        'category_id' => 'required|exists:podcast_categories,id', // New validation
        'youtube_url' => 'nullable|string', // New validation for YouTube URL
        'upload_date' => 'required|date', // New validation for upload date
        'publish_date' => 'required|date|after_or_equal:upload_date', // Ensure publish date is not before upload date
    ]);

    // Determine whether to use an existing podcast ID or create a new one
    if (trim($request->podcast_id) !== '') {
        // Existing podcast selected
        $podcastId = $request->podcast_id;
    } else {
        // No podcast selected, generate a new podcast ID
        $podcastId = 'PODCAST' . rand(10000, 99999);
    }

    // Handle the file upload
    if ($request->hasFile('image') && $request->hasFile('music')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $musicPath = $request->file('music')->store('music', 'public');
    } else {
        return redirect()->back()->with('error', 'File upload failed.');
    }

    // Create a new podcast record
    $podcast = new Podcast();
    $podcast->name = $request->name;
    $podcast->podcast_id = $podcastId; // Ensure this is saved correctly
    $podcast->language = $request->language;
    $podcast->description = $request->description;
    $podcast->image = $imagePath;
    $podcast->music = $musicPath;
    $podcast->podcast_category_id = $request->category_id; // Save category ID
    $podcast->youtube_url = $request->youtube_url; // Save YouTube URL
    $podcast->upload_date = $request->upload_date; // Save upload date
    $podcast->publish_date = $request->publish_date; // Save publish date
    $podcast->save();

    return redirect()->route('addpodcast')->with('success', 'Podcast created successfully.');
}

    
    
    
    public function editpodcast(Podcast $podcast)
    {
        return view('admin/editpodcast', compact('podcast'));
    }

    public function updatepodcast(Request $request, Podcast $podcast)
{
    // Validate the incoming request data
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'music' => 'nullable|mimes:mp3,wav|max:100000000',
        'language' => 'required|string|in:odia,english,hindi', // Validate language input
    ]);

    // Handle image file upload if it exists
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
        $imagePath = $request->file('image')->store('images', 'public');
        $podcast->image = $imagePath;
    }

    // Handle music file upload if it exists
    if ($request->hasFile('music') && $request->file('music')->isValid()) {
        $musicPath = $request->file('music')->store('music', 'public');
        $podcast->music = $musicPath;
    }

    // Update the language field and other fields
    $podcast->language = $request->language;

    // Update the podcast with the validated data
    $podcast->update($request->only(['name', 'description']));

    // Save the podcast model after updating fields directly
    $podcast->save();

    // Redirect with success message
    return redirect()->route('managepodcast')->with('success', 'Podcast updated successfully');
}

    public function destroy(Podcast $podcast)
    {
        $podcast->delete();
        return redirect()->back()->with('danger', 'Podcast delete successfully.');
    }


    public function managepodcastcategory()
    {
        // Fetch categories where status is 'active'
        $categories = PodcastCategory::where('status', 'active')->get();
    
        // Pass the categories to the view
        return view('admin.mngpodcastcategory', compact('categories'));
    }

    public function saveCategory(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'category_name' => 'required|string|max:255',
            'category_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
        ]);

        // Handle file upload if exists
        $imagePath = null;
        if ($request->hasFile('category_img')) {
            $imagePath = $request->file('category_img')->store('category_images', 'public');
        }

        // Save category data
        PodcastCategory::create([
            'category_name' => $request->category_name,
            'category_img' => $imagePath,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Category added successfully!');
    }


    public function updateCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:podcast_categories,id',
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $category = PodcastCategory::find($request->id);

        // Update name and description
        $category->category_name = $request->category_name;
        $category->description = $request->description;

        // Update image if a new one is uploaded
        if ($request->hasFile('category_img')) {
            // Delete old image if exists
            if ($category->category_img && Storage::exists('public/' . $category->category_img)) {
                Storage::delete('public/' . $category->category_img);
            }

            // Store new image and update path
            $path = $request->file('category_img')->store('categories', 'public');
            $category->category_img = $path;
        }

        $category->save();

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    // Method to soft delete category by setting status to 'deleted'
    public function deleteCategory($id)
    {
        $category = PodcastCategory::find($id);

        if ($category) {
            $category->status = 'deleted';
            $category->save();

            return redirect()->back()->with('success', 'Category deleted successfully.');
        }

        return redirect()->back()->with('error', 'Category not found.');
    }
}
