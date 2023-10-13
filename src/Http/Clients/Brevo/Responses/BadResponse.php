<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses;

use Illuminate\Http\Client\Response;

class BadResponse
{
    private function __construct(public Response $response, public int $status, public string $error, public string $code)
    {
    }

    public static function fromResponse(Response $response): BadResponse
    {
        return new self(
            $response,
            $response->status(),
            $response->json()['message'] ?? 'no error message provided',
            $response->json()['code'] ?? ''
        );
    }

    public function success(): bool
    {
        return false;
    }

    public function bad(): bool
    {
        return ! $this->success();
    }
}
