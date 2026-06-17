<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $isMediaStream = $request->routeIs('course-session-items.media.stream');
        $isMediaView = $request->routeIs('course-session-items.media.view');
        $isEmbeddedMediaView = $isMediaView && $request->boolean('embed');
        $allowsSameOriginFrame = $isMediaStream || $isEmbeddedMediaView;
        $allowsYoutubeEmbeds = $request->routeIs('dashboard', 'demo-review-videos.*');
        $response->headers->set('X-Frame-Options', $allowsSameOriginFrame ? 'SAMEORIGIN' : 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-XSS-Protection', '0');
        $frameAncestors = $allowsSameOriginFrame ? "frame-ancestors 'self'" : "frame-ancestors 'none'";
        $frameSources = ["'self'"];

        if ($isMediaView) {
            array_push($frameSources, 'https://res.cloudinary.com', 'https://*.cloudinary.com');
        }

        if ($allowsYoutubeEmbeds) {
            array_push($frameSources, 'https://www.youtube.com', 'https://youtube.com', 'https://www.youtube-nocookie.com', 'https://youtube-nocookie.com');
        }

        $frameSrc = 'frame-src ' . implode(' ', array_unique($frameSources));
        $mediaSrc = $isMediaView ? "media-src 'self' https://res.cloudinary.com https://*.cloudinary.com" : "media-src 'self'";
        $imgSrc = $isMediaView ? "img-src 'self' data: https://res.cloudinary.com https://*.cloudinary.com" : "img-src 'self' data:";

        $isLocal = app()->environment('local');
        $viteScript = $isLocal ? ' http://localhost:5173' : '';
        $viteStyle  = $isLocal ? ' http://localhost:5173' : '';
        $viteFont   = $isLocal ? ' http://localhost:5173' : '';
        $viteWs     = $isLocal ? ' ws://localhost:5173 http://localhost:5173' : '';

        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            $frameAncestors,
            $frameSrc,
            $mediaSrc,
            "img-src 'self' data: blob: https://api.dicebear.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline'"
                . $viteStyle
                . " https://cdnjs.cloudflare.com"
                . " https://cdn.jsdelivr.net"
                . " https://fonts.googleapis.com",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'"
                . $viteScript
                . " https://cdnjs.cloudflare.com"
                . " https://cdn.jsdelivr.net"
                . " https://static.cloudflareinsights.com",
            "connect-src 'self'"
                . $viteWs
                . " https://lms.academicmantraservices.com"
                . " https://cloudflareinsights.com",
            "font-src 'self' data:"
                . $viteFont
                . " https://cdnjs.cloudflare.com"
                . " https://cdn.jsdelivr.net"
                . " https://fonts.gstatic.com",
            "object-src 'none'",
        ]));
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
