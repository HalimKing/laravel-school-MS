<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        // Convert comma-separated permissions to array
        $permissionsArray = array_map('trim', explode(',', implode(',', $permissions)));

        if (!auth()->user()->hasAnyPermission($permissionsArray)) {
            abort(403, 'Unauthorized - You do not have the required permission.');
        }

        return $next($request);
    }
}
