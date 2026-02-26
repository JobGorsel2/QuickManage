<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutAGOLController extends Controller
{
    public function logout(Request $request)
    {
        // Remove ArcGIS session data
        $request->session()->forget([
            'arcgis.access_token',
            'arcgis.expires_in',
            'arcgis.username',
        ]);
 
        // Invalidate session & CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // dd('logoutController');
        return redirect('/')->with('status', 'Logged out successfully.');
    }
}