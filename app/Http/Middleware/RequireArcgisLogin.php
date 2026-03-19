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
        // If NOT connected to ArcGIS or token has expired
        if (!$request->session()->has('arcgis.access_token')) {
            return redirect()->route('arcgis.loginAGOL');
        }

        $expiresAt = $request->session()->get('arcgis.expires_at');
        if ($expiresAt && now()->timestamp >= $expiresAt) {
            $request->session()->forget(['arcgis.access_token', 'arcgis.expires_in', 'arcgis.expires_at', 'arcgis.username']);
            return redirect()->route('arcgis.loginAGOL')->with('error', 'ArcGIS sessie verlopen, log opnieuw in.');
        }

        return $next($request);
    }
}
