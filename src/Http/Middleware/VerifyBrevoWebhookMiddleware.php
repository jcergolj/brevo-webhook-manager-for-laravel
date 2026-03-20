<?php

namespace Jcergolj\BrevoWebhookManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\IpUtils;

class VerifyBrevoWebhookMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $validCIDRs = [
            '1.179.112.0/20',
            '172.246.240.0/20',
        ];

        $ip = $request->ip();

        if (IpUtils::checkIp($ip, $validCIDRs)) {
            return $next($request);
        }

        abort(Response::HTTP_BAD_REQUEST, 'Bad incoming Brevo request.');
    }
}
