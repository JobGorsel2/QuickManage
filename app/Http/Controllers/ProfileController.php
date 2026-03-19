<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit($id) 
    {
        if (Auth::id() != $id) {
            abort(403);
        }

        return view('pages.profile.edit', [
               
        ]);
    }
    public function update(Request $request, $id) 
    {
        if (Auth::id() != $id) {
            abort(403);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $data = User::findOrFail($id);
        if ($request->filled('name')) { $data->name = $request['name']; }
        if ($request->filled('email')) { $data->email = $request['email']; }
        if ($request->hasFile('image')) { $data->image = base64_encode(file_get_contents($request->file('image'))); }
        $data->update();

        return back()->with('success', 'Profiel bijgewerkt!');
    }
}
