<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses;

use Illuminate\Http\Client\Response;
use Jcergolj\BrevoWebhookManager\Collections\Webhooks;

class FetchAllWebhookResponse
{
    use HasStatus;

    private function __construct(
        public Response $original,
        public int $status,
        public Webhooks $webhooks,
    ) {
    }

    public static function fromResponse(Response $response): FetchAllWebhookResponse
    {
        return new self(
            $response,
            $response->status(),
            new Webhooks($response->json()['webhooks']),
        );
    }
}
