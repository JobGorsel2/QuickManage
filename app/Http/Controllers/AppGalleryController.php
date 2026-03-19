<?php

namespace App\Http\Controllers;
use App\Models\AppCategorie;
use Illuminate\Http\Request;

class AppGalleryController extends Controller
{
     public function index() 
    {   
        $app_categories = AppCategorie::all();
       

        return view('pages.app_gallery.index', [
            'app_categories' => $app_categories
        ]);
    }

    public function create() 
    {
        

        return view('pages.app_gallery.app_category.create_cat', [
                
        ]);
    }

      public function store(Request $request) 
    {
       
        $app_category = new AppCategorie();
        $app_category->category_name = $request['category_name'];
        $app_category->save();

        return back()->with('success','Categorie aangemaakt!');
    }
   
     
}
