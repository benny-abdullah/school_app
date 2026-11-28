<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Kepatuhan Standar Keamanan ISO/IEC 27001:2022 Klausul A.8.26 (Application Security)
     * Menginjeksikan HTTP Security Headers pada setiap respon HTTP untuk proteksi Clickjacking,
     * XSS, MIME Sniffing, dan isolasi izin perangkat keras (WebRTC Kamera Pemindai).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Proteksi Clickjacking (ISO 27001 / OWASP A05)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Proteksi MIME-type Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Proteksi Filter XSS Browser
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Pengendalian Referrer (Mencegah Kebocoran URL Sensitif)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Isolasi Izin Perangkat: Izinkan kamera untuk WebRTC pemindai barcode, blokir microphone & GPS
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=()');

        // 6. Strict Transport Security (HSTS) jika koneksi HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
