<?php

namespace Jcergolj\BrevoWebhookManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;

class VerifyBrevoWebhookMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $validCIDRs = (array) config('brevo-webhook-manager.valid_cidrs', []);

        if (config('brevo-webhook-manager.allow_cloudflare')) {
            $validCIDRs = array_merge($validCIDRs, (array) config('brevo-webhook-manager.cloudflare_cidrs', []));
        }

        $ip = $request->ip();

        if (IpUtils::checkIp($ip, $validCIDRs)) {
            return $next($request);
        }

        Log::warning('Brevo webhook rejected.', [
            'ip' => $ip,
            'ips' => $request->ips(),
            'x_forwarded_for' => $request->header('X-Forwarded-For'),
            'url' => $request->fullUrl(),
        ]);

        abort(Response::HTTP_BAD_REQUEST, 'Bad incoming Brevo request.');
    }
}
