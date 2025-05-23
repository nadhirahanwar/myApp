<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role, $permission = null)
    {
        $user = Auth::user();

        // Check if the user has the required role
        if (!$user->roles()->where('role_name', $role)->exists()) {
            return redirect('/home')->withErrors('You do not have permission to access this page.');
        }

        // If a permission is provided, check if the user has that permission
        if ($permission && !$user->permissions()->where('permission_name', $permission)->exists()) {
            return redirect('/home')->withErrors('You do not have the required permission.');
        }

        return $next($request);
    }
}
