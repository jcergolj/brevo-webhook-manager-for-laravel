<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo;

use Illuminate\Support\Facades\Http;

class BrevoMacro
{
    public function presetting(): callable
    {
        return function () {
            return Http::withHeaders(
                [
                    'api-key' => config('brevo-webhook-manager.brevo.api_key'),
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ]
            )->withUserAgent(config('brevo-webhook-manager.api_user_agent'))
                ->baseUrl(config('brevo-webhook-manager.brevo.base_url'));
        };
    }
}
