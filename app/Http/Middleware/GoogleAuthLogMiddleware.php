<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class GoogleAuthLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Journaliser les requêtes entrantes
        Log::channel('google_auth')->info('Requête entrante Google Auth', [
            'uri' => $request->getUri(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'query' => $request->query->all(),
        ]);

        // Continuer le traitement
        $response = $next($request);

        // Journaliser la réponse
        Log::channel('google_auth')->info('Réponse sortante Google Auth', [
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }
}
