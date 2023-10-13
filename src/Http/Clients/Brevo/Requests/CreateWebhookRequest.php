<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes\CreateAttribute;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\CreateWebhookResponse;

class CreateWebhookRequest
{
    public function __construct(public Factory $client)
    {
    }

    public function send(CreateAttribute $attribute): BadResponse|CreateWebhookResponse
    {
        $response = $this->client->presetting()
            ->post('webhooks', $attribute->toArray());

        if ($response->status() === Response::HTTP_CREATED) {
            return CreateWebhookResponse::fromResponse($response);
        }

        return BadResponse::fromResponse($response);
    }
}
