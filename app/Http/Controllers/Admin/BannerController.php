<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppBanner;


class BannerController extends Controller
{
    //
    public function manageappbanner(){
        $banners = AppBanner::all();
        return view('admin/manageappbanner',compact('banners'));
    }
    public function addbanner(){
        return view('admin/addappbanner');
    }

    public function savebanner(Request $request){
        $bannerdata = new AppBanner();
        if($request->hasFile('banner_img')){

            $path = 'uploads/banner/'.$bannerdata->banner_img;
           
           
            $file = $request->file('banner_img');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('uploads/banner/',$filename);
            $bannerdata->banner_img=$filename;
          }


       
        $bannerdata->title_text = $request->title_text;
        $bannerdata->alt_text = $request->alt_text;

        $bannerdata->save();
        
        return redirect()->back()->with('success', 'Data saved successfully.');
    }
    public function editbanner(AppBanner $banner)
    {
        return view('admin/editbanner', compact('banner'));
    }
    public function updatebanner(Request $request, $id)
    {
        // Retrieve the existing banner record
        $bannerdata = AppBanner::find($id);
        if (!$bannerdata) {
            return redirect()->back()->with('error', 'Banner not found.');
        }

        // Check if a new banner image file is uploaded
        if ($request->hasFile('banner_img')) {
            // Delete the old image if it exists
            $oldImagePath = 'uploads/banner/' . $bannerdata->banner_img;
            if (file_exists($oldImagePath) && !empty($bannerdata->banner_img)) {
                unlink($oldImagePath);
            }

            // Handle the new image upload
            $file = $request->file('banner_img');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('uploads/banner/', $filename);
            $bannerdata->banner_img = $filename;
        }

        // Update other banner properties
        $bannerdata->title_text = $request->title_text;
        $bannerdata->alt_text = $request->alt_text;

        // Save the updated record
        $bannerdata->save();

        return redirect()->route('manageappbanner')->with('success', 'Banner updated successfully.');
    }
    public function deletebanner($id)
    {
        $bannerdata = AppBanner::find($id);
        if (!$bannerdata) {
            return redirect()->back()->with('error', 'Banner not found.');
        }

        $imagePath = 'uploads/banner/' . $bannerdata->banner_img;
        if (file_exists($imagePath) && !empty($bannerdata->banner_img)) {
            unlink($imagePath);
        }

        $bannerdata->delete();

        return redirect()->back()->with('success', 'Banner deleted successfully.');
    }
}
