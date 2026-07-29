<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Check if the authenticated user has the required permission section.
     */
    public function handle(Request $request, Closure $next, string $section): Response
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermission($section)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Permission denied'], 403);
            }
            return response()->view('admin.no_permission', [], 403);
        }

        return $next($request);
    }
}
