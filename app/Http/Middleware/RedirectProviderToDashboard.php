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
        // Redirect ALL admin routes (except excluded ones) to provider dashboard
        if (auth()->check() &&
            auth()->user()->hasRole('Provider') &&
            $request->is('admin*') &&
            !$request->is('admin/provider-dashboard*') &&
            !$request->is('admin/logout*') &&
            !$request->is('admin/password-reset*') &&
            !$request->ajax()) {

            \Illuminate\Support\Facades\Log::info('[RedirectProviderToDashboard] Redirecting Provider user to provider dashboard', [
                'user_id' => auth()->id(),
                'email' => auth()->user()->email,
                'from_url' => $request->fullUrl(),
                'to_url' => '/admin/provider-dashboard'
            ]);

            return redirect('/admin/provider-dashboard');
        }

        return $next($request);
    }
}
