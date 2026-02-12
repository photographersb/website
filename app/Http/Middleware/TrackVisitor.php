<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tracking for static assets to avoid rate limiting issues with social crawlers
        $staticAssetPattern = '/\.(css|js|jpg|jpeg|gif|png|ico|gz|svg|svgz|ttf|otf|woff|woff2|eot|mp4|ogg|ogv|webm|webp|zip|swf|map)$/i';
        
        // Only track web routes (not API, admin, or static assets)
        if (!$request->is('api/*') && !$request->is('admin/*') && !str_starts_with($request->path(), '_') && !preg_match($staticAssetPattern, $request->path())) {
            $this->trackVisitor($request);
        }
        
        return $next($request);
    }
    
    /**
     * Track visitor activity
     */
    protected function trackVisitor(Request $request): void
    {
        try {
            $sessionId = session()->getId();
            $now = now();
            
            // Get or create visitor log
            $visitor = DB::table('visitor_logs')
                ->where('session_id', $sessionId)
                ->first();
            
            if (!$visitor) {
                // New visitor
                $visitorId = DB::table('visitor_logs')->insertGetId([
                    'session_id' => $sessionId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'device_type' => $this->getDeviceType($request->userAgent()),
                    'browser' => $this->getBrowser($request->userAgent()),
                    'os' => $this->getOS($request->userAgent()),
                    'referrer' => $request->header('referer'),
                    'landing_page' => $request->fullUrl(),
                    'first_visit' => $now,
                    'last_activity' => $now,
                    'page_views' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                // Returning visitor
                $visitorId = $visitor->id;
                DB::table('visitor_logs')
                    ->where('id', $visitorId)
                    ->update([
                        'last_activity' => $now,
                        'page_views' => DB::raw('page_views + 1'),
                        'updated_at' => $now,
                    ]);
            }
            
            // Record page view
            DB::table('page_views')->insert([
                'visitor_log_id' => $visitorId,
                'url' => $request->fullUrl(),
                'page_title' => $this->getPageTitle($request->path()),
                'referrer' => $request->header('referer'),
                'time_on_page' => 0,
                'viewed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
        } catch (\Exception $e) {
            // Silently fail - don't break the application
            \Log::warning('Visitor tracking failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Determine device type from user agent
     */
    protected function getDeviceType(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }
        
        $userAgent = strtolower($userAgent);
        
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $userAgent)) {
            return 'tablet';
        }
        
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)) {
            return 'mobile';
        }
        
        return 'desktop';
    }
    
    /**
     * Extract browser from user agent
     */
    protected function getBrowser(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }
        
        $browsers = [
            '/edg/i' => 'Edge',
            '/chrome/i' => 'Chrome',
            '/safari/i' => 'Safari',
            '/firefox/i' => 'Firefox',
            '/msie/i' => 'Internet Explorer',
            '/trident/i' => 'Internet Explorer',
            '/opera/i' => 'Opera',
        ];
        
        foreach ($browsers as $regex => $browser) {
            if (preg_match($regex, $userAgent)) {
                return $browser;
            }
        }
        
        return 'Other';
    }
    
    /**
     * Extract OS from user agent
     */
    protected function getOS(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }
        
        $osList = [
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 11/i' => 'Windows 11',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows/i' => 'Windows',
            '/macintosh|mac os x/i' => 'macOS',
            '/mac_powerpc/i' => 'Mac OS',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iOS',
            '/ipad/i' => 'iOS',
            '/ipod/i' => 'iOS',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
        ];
        
        foreach ($osList as $regex => $os) {
            if (preg_match($regex, $userAgent)) {
                return $os;
            }
        }
        
        return 'Other';
    }
    
    /**
     * Get friendly page title from path
     */
    protected function getPageTitle(string $path): string
    {
        if ($path === '/') {
            return 'Home';
        }
        
        $titles = [
            'competitions' => 'Competitions',
            'photographers' => 'Photographers',
            'gallery' => 'Gallery',
            'about' => 'About',
            'contact' => 'Contact',
            'pricing' => 'Pricing',
            'login' => 'Login',
            'register' => 'Register',
        ];
        
        foreach ($titles as $segment => $title) {
            if (str_contains($path, $segment)) {
                return $title;
            }
        }
        
        return ucfirst(trim($path, '/'));
    }
}
