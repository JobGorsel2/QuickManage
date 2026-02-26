<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WidgetsController extends Controller
{
    public function index()
    {
        $pages = Widget::get();
        // $var = 'TEST'; 
        // dd($pages);
        return view('pages.pages.index',[
            'pages' => $pages,
        ]);
    }
}
