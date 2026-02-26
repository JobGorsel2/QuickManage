<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireArcgisLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next)
    {
     
        // If NOT connected to ArcGIS
        if (!$request->session()->has('arcgis.access_token')) {
            return redirect()->route('arcgis.loginAGOL');
        }

        return $next($request);
    }
}
