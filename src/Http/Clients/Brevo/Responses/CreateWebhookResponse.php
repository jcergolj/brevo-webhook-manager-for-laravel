<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses;

use Illuminate\Http\Client\Response;

class CreateWebhookResponse
{
    use HasStatus;

    private function __construct(public Response $original, public int $status, public int $id)
    {
    }

    public static function fromResponse(Response $response): CreateWebhookResponse
    {
        return new self(
            $response,
            $response->status(),
            $response->json()['id']
        );
    }
}
