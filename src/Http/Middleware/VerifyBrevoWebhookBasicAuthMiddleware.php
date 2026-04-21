<?php

namespace Jcergolj\BrevoWebhookManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class VerifyBrevoWebhookBasicAuthMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $username = config('brevo-webhook-manager.auth.username');
        $password = config('brevo-webhook-manager.auth.password');

        if (! is_string($username) || $username === '' || ! is_string($password) || $password === '') {
            Log::error('Brevo webhook basic auth credentials are not configured.');

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Brevo webhook credentials not configured.');
        }

        $providedUser = (string) $request->getUser();
        $providedPass = (string) $request->getPassword();

        $userMatch = hash_equals($username, $providedUser);
        $passMatch = hash_equals($password, $providedPass);

        if ($userMatch && $passMatch) {
            return $next($request);
        }

        Log::warning('Brevo webhook basic auth failed.', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
        ]);

        abort(Response::HTTP_UNAUTHORIZED, 'Invalid Brevo webhook credentials.');
    }
}
