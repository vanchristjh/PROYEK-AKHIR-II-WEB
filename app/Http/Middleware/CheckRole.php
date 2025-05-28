<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Log for debugging
        Log::info('CheckRole middleware', [
            'user' => $request->user()->id ?? 'guest',
            'required_role' => $role,
            'user_roles' => $request->user() ? $request->user()->roles->pluck('name') : []
        ]);
        
        if (!$request->user() || !$request->user()->hasRole($role)) {
            // Redirect to unauthorized page if user doesn't have required role
            return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}
