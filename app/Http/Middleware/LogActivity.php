<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $response = $next($request);

        // Filtra peticiones que no queremos registrar (ver método abajo)
        if ($this->shouldLog($request)) {

            $forwarded = $request->header('X-Forwarded-For');
            $ips = $forwarded ? array_map('trim', explode(',', $forwarded)) : [];

            $publicIps = array_filter($ips, function ($ip) {
                return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
            });

            // Intentar primero una IPv4 pública
            $ipv4 = array_filter($publicIps, fn($ip) => filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));

            if ($ipv4) {
                $ip = reset($ipv4); // primera IPv4 válida
            } elseif ($publicIps) {
                $ip = end($publicIps); // última pública (por si todas son IPv6)
            } else {
                $ip = $request->ip();
            }

            Log::create([
                'ip_address' => $ip,
                'action'     => $request->method(),
                'state'      => (string) $response->getStatusCode(),
                'details'    => json_encode($request->headers->all()),
                'user_id'    => Auth::id(),
            ]);
        }

        return $response;
    }

    //filtro para cosas que no queremos registrar
    protected function shouldLog(Request $request): bool
    {
        //ignorar arhivos 
        $ignoredExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'map'];

        $extension = pathinfo($request->path(), PATHINFO_EXTENSION);
        if (in_array(strtolower($extension), $ignoredExtensions)) {
            return false;
        }

        return true;
    }
}
