<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectProviderToDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has Provider role
        if (auth()->check() && 
            auth()->user()->hasRole('Provider') && 
            $request->is('admin') && 
            !$request->is('admin/documentacion*') &&
            !$request->is('admin/logout*') &&
            !$request->is('admin/password-reset*') &&
            !$request->ajax()) {
            
            \Illuminate\Support\Facades\Log::info('[RedirectProviderToDashboard] Redirecting Provider user from /admin to /admin/documentacion', [
                'user_id' => auth()->id(),
                'email' => auth()->user()->email,
                'current_url' => $request->fullUrl()
            ]);
            
            return redirect('/admin/documentacion');
        }
        
        return $next($request);
    }
}
