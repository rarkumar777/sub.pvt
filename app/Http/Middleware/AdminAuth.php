<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     * Checks if the user is authenticated and has admin permission.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            $returnUrl = urlencode($request->fullUrl());
            return redirect()->route('login', ['ret' => $returnUrl]);
        }

        if (!$user->isAdmin()) {
            return redirect()->route('login', ['ret' => urlencode($request->fullUrl())]);
        }

        // Share user data with all views
        view()->share('authUser', $user);
        view()->share('GOGIES', $this->getLegacyConfig());

        return $next($request);
    }

    /**
     * Get legacy-compatible config array for views.
     */
    private function getLegacyConfig(): array
    {
        return [
            'url' => rtrim(config('app.url'), '/'),
            'admin_url' => rtrim(config('app.url'), '/') . '/admin/',
            'lang' => app()->getLocale(),
            'admin_lang' => 'en',
            'theme' => 'pvt',
            'active_langs' => ['en', 'Ar', 'es', 'fr', 'ge', 'it', 'pt'],
            'is_admin' => true,
            'is_user' => true,
            'currency' => 78,
        ];
    }
}
