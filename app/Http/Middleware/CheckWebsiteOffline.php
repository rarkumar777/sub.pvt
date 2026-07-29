<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckWebsiteOffline
{
    public function handle(Request $request, Closure $next)
    {
        // Skip check for admin routes
        if ($request->is('admin/*') || $request->is('admin')) {
            return $next($request);
        }

        // Read global config
        $configPath = base_path('../pvt.jo/config/global.php');
        if (file_exists($configPath)) {
            $content = file_get_contents($configPath);
            if (preg_match("/\\\$GOGIES\['WEB_SITE_OFFLINE'\]='([^']*)'/", $content, $m)) {
                if ($m[1] === 'off') {
                    // Website is turned off - show maintenance page
                    return response()->view('frontend.offline', [], 503);
                }
            }
        }

        // Language Default Override Logic
        $defaultLang = 'en';
        if (file_exists($configPath) && isset($content)) {
            if (preg_match("/\\\$GOGIES\['lang'\]='([^']*)'/", $content, $m)) {
                $defaultLang = $m[1] ?: 'en';
            }
        }
        
        $lastAdminLang = session('app_global_default_lang');
        
        if ($lastAdminLang && $lastAdminLang !== $defaultLang) {
            session(['app_global_default_lang' => $defaultLang]);
            $segments = $request->segments();
            
            if (count($segments) > 0 && strlen($segments[0]) <= 2) {
                if ($segments[0] !== $defaultLang) {
                    $segments[0] = $defaultLang;
                    $url = '/' . implode('/', $segments);
                    if ($request->getQueryString()) {
                        $url .= '?' . $request->getQueryString();
                    }
                    return redirect($url);
                }
            }
        } else if (!$lastAdminLang) {
            session(['app_global_default_lang' => $defaultLang]);
        }

        return $next($request);
    }
}
