<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit($id) 
    {
        // Auth::user()->email;
        //redirect
        
        return view('pages.profile.edit', [
               
        ]);
    }
    public function update(Request $request,$id) 
    {
 
        $request->validate([
            'name' => ' ',
            'email' => ' ',
            'image' => ' ',
        ]);

        if($request['image']) {
        $image = base64_encode(file_get_contents($request->file('image')));
       
          
        } 
        else {
            $image = '';
        }
        $data = User::find($id);
        if($request['name']){ $data->name = $request['name'] ;}else{ }
        if($request['email']){ $data->email = $request['email'] ;}else{ }
        if($request['image']){ $data->image = base64_encode(file_get_contents($request->file('image'))) ;}else{ }
        $data->update();
        // dd($image);
        // Auth::user()->email;
        //redirect
        return back()->with('success','Profiel bijgewerkt!');
    }
}
