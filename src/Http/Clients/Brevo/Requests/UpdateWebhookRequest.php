<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes\UpdateAttribute;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\UpdateWebhookResponse;

class UpdateWebhookRequest
{
    public function __construct(public Factory $client)
    {
    }

    public function send(int $id, UpdateAttribute $attribute): BadResponse|UpdateWebhookResponse
    {
        $response = $this->client->presetting()
            ->put("webhooks/{$id}", $attribute->toArray());

        if ($response->status() === Response::HTTP_NO_CONTENT) {
            return UpdateWebhookResponse::fromResponse($response);
        }

        return BadResponse::fromResponse($response);
    }
}
