<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses;

use Illuminate\Http\Client\Response;

class DeleteWebhookResponse
{
    use HasStatus;

    private function __construct(public Response $original, public int $status)
    {
    }

    public static function fromResponse(Response $response): DeleteWebhookResponse
    {
        return new self(
            $response,
            $response->status(),
        );
    }
}
