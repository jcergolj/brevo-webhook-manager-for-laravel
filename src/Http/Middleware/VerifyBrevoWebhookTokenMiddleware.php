<?php

namespace Jcergolj\BrevoWebhookManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class VerifyBrevoWebhookTokenMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $expected = config('brevo-webhook-manager.auth.token');

        if (! is_string($expected) || $expected === '') {
            Log::error('Brevo webhook token is not configured.');

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Brevo webhook token not configured.');
        }

        $provided = $this->extractToken($request);

        if ($provided !== null && hash_equals($expected, $provided)) {
            return $next($request);
        }

        Log::warning('Brevo webhook token auth failed.', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
        ]);

        abort(Response::HTTP_UNAUTHORIZED, 'Invalid Brevo webhook token.');
    }

    private function extractToken(Request $request): ?string
    {
        $headerName = (string) config('brevo-webhook-manager.auth.token_header', 'Authorization');

        $header = $request->header($headerName);

        if (! is_string($header) || $header === '') {
            return null;
        }

        if (strcasecmp($headerName, 'Authorization') === 0 && stripos($header, 'Bearer ') === 0) {
            return trim(substr($header, 7)) ?: null;
        }

        return $header;
    }
}
