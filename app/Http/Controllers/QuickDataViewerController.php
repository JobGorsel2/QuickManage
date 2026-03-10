<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class QuickDataViewerController extends Controller
{ 


 public function index()
    { 
        
        return view('quickdataviewer.index',[
       
            
        ]);
    }
}
