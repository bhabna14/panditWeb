<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoojaUnit;
use App\Models\PanditTitle;

class TitleController extends Controller
{
    //

    public function managetitle(){
        $poojatitles = PanditTitle::where('status', 'active')->get();
        return view('admin/managetitle',compact('poojatitles'));
        // return view('admin/managetitle');
    }
    public function savetitle(Request $request){
        $poojatitle = new PanditTitle();
        
        $poojatitle->pandit_title = $request->pandit_title;
       
        $poojatitle->status  = 'active' ;
        $poojatitle->save();
        
        return redirect()->back()->with('success', 'Data saved successfully.');
      
    }
    public function edittitle(PoojaUnit $title)
    {
        return view('admin/managetitle', compact('title'));
    }
    public function updatetitle(Request $request)
    {
           
            $poojatitle = PanditTitle::find($request->id);
            $poojatitle->pandit_title = $request->pandit_title;
            $poojatitle->save();

            return redirect()->back()->with('success', 'Item updated successfully');
    }
    public function dlttitle(Request $request,$title)
    {
        $poojatitle = PanditTitle::find($title);
        $poojatitle->status  = 'deactive' ;
          $poojatitle->update();
    
                return redirect()->back()->with('success', 'Data delete successfully.');
    
    }
}
