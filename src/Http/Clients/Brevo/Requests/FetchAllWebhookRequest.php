<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\FetchAllWebhookResponse;

class FetchAllWebhookRequest
{
    public function __construct(public Factory $client)
    {
    }

    public function send(string $type): BadResponse|FetchAllWebhookResponse
    {
        $response = $this->client->presetting()->get('webhooks', ['type' => $type]);

        if ($response->status() === Response::HTTP_OK) {
            return FetchAllWebhookResponse::fromResponse($response);
        }

        return BadResponse::fromResponse($response);
    }
}
