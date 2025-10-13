<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;

class SecureCookieMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip cookie processing for file downloads
        if ($response instanceof BinaryFileResponse) {
            // Still add security headers for file downloads
            $this->addSecurityHeaders($response);
            return $response;
        }

        // Get all cookies from the response
        $cookies = $response->headers->getCookies();

        // Clear existing cookies
        $response->headers->removeCookie('Set-Cookie');

        foreach ($cookies as $cookie) {
            // Create a new secure cookie with all security attributes
            $secureCookie = new Cookie(
                $this->getSecureCookieName($cookie->getName()),
                $cookie->getValue(),
                $cookie->getExpiresTime(),
                '/', // path - restrict to root for __Host- prefix
                $cookie->getDomain(),
                true, // secure - only send over HTTPS
                true, // httpOnly - prevent XSS attacks
                false, // raw
                $this->getSameSiteAttribute() // sameSite
            );

            $response->headers->setCookie($secureCookie);
        }

        // Add additional security headers
        $this->addSecurityHeaders($response);

        return $response;
    }

    /**
     * Add security headers to response
     */
    private function addSecurityHeaders($response): void
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    /**
     * Get secure cookie name with __Host- prefix for enhanced security
     */
    private function getSecureCookieName(string $name): string
    {
        // Add __Host- prefix if not already present and if using HTTPS
        if (!str_starts_with($name, '__Host-') && $this->isHttps()) {
            return '__Host-' . $name;
        }

        return $name;
    }

    /**
     * Get SameSite attribute value
     */
    private function getSameSiteAttribute(): string
    {
        return config('session.same_site', 'strict');
    }

    /**
     * Check if the request is using HTTPS
     */
    private function isHttps(): bool
    {
        return request()->isSecure() || config('app.env') === 'production';
    }
}