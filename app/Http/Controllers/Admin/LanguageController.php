<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;


class LanguageController extends Controller
{
    //
    public function managelang(){
        $languages = Language::where('status', 'active')->get();
        return view('admin/managelang',compact('languages'));
    }
    public function addlang(){
        return view('admin/addlang');
    }
    public function savelang(Request $request){
        $language = new Language();
        
        $language->lang_name = $request->lang_name;
       
        $language->status  = 'active' ;
        $language->save();
        
        return redirect()->back()->with('success', 'Data saved successfully.');
      
    }
    public function editlang(Language $lang)
    {
        return view('admin/managelang', compact('lang'));
    }
    public function updatelang(Request $request)
    {
           
            $language = Language::find($request->id);
            $language->lang_name = $request->lang_name;
            $language->save();

            return redirect()->back()->with('success', 'Data updated successfully');
    }
    public function dltlang(Request $request,$lang)
    {
        $language = Language::find($lang);
        $language->status  = 'deactive' ;
          $language->update();
    
                return redirect()->back()->with('success', 'Data delete successfully.');
    
    }
}
