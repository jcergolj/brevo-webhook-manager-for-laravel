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
            ['start' => ip2long('1.179.112.0'), 'end' => ip2long('1.179.127.255')],
            ['start' => ip2long('172.246.240.0'), 'end' => ip2long('172.246.255.255')],
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
