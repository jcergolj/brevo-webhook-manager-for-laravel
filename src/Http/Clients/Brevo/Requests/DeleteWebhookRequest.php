<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\DeleteWebhookResponse;

class DeleteWebhookRequest
{
    public function __construct(public Factory $client)
    {
    }

    public function send(int $id): BadResponse|DeleteWebhookResponse
    {
        $response = $this->client->presetting()
            ->delete("webhooks/{$id}");

        if ($response->status() === Response::HTTP_NO_CONTENT) {
            return DeleteWebhookResponse::fromResponse($response);
        }

        return BadResponse::fromResponse($response);
    }
}
