<?php

namespace Jcergolj\BrevoWebhookManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyBrevoWebhookMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $validIPRanges = [
            ['start' => ip2long('185.107.232.0'), 'end' => ip2long('185.107.232.255')],
            ['start' => ip2long('1.179.112.2'), 'end' => ip2long('1.179.127.254')],
        ];

        foreach ($validIPRanges as $range) {
            if ($this->insideRange(ip2long($request->ip()), $range)) {
                return $next($request);
            }
        }

        abort(Response::HTTP_BAD_REQUEST, 'Bad incoming sendinblue request.');
    }

    /**
     * @param  int  $ip
     * @param  array  $range
     * @return bool
     */
    protected function insideRange($ip, $range)
    {
        return $ip >= $range['start'] && $ip <= $range['end'];
    }
}
