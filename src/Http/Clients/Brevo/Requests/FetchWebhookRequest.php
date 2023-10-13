<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\FetchWebhookResponse;

class FetchWebhookRequest
{
    public function __construct(public Factory $client)
    {
    }

    public function send(int $id): BadResponse|FetchWebhookResponse
    {
        $response = $this->client->presetting()->get("webhooks/{$id}");

        if ($response->status() === Response::HTTP_OK) {
            return FetchWebhookResponse::fromResponse($response);
        }

        return BadResponse::fromResponse($response);
    }
}
