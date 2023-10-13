<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses;

use Illuminate\Http\Client\Response;
use Jcergolj\BrevoWebhookManager\Items\Webhook;

class FetchWebhookResponse
{
    use HasStatus;

    private function __construct(
        public Response $original,
        public int $status,
        public Webhook $webhook,
    ) {
    }

    public static function fromResponse(Response $response): FetchWebhookResponse
    {
        return new self(
            $response,
            $response->status(),
            Webhook::fromArray($response->json())
        );
    }
}
