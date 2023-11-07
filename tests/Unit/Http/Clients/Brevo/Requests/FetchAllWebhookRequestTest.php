<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Brevo;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\FetchAllWebhookRequest;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\FetchAllWebhookResponse;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class FetchAllWebhookRequestTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    /** @test */
    public function send_successful()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url').'webhooks*' => Http::response([
                'webhooks' => [
                    [
                        'id' => 123,
                        'url' => '',
                        'description' => '',
                        'events' => [],
                        'type' => WebhookTypes::TRANSACTIONAL->value,
                        'createdAt' => '',
                        'modifiedAt' => '',
                    ],
                    [
                        'id' => 456,
                        'url' => '',
                        'description' => '',
                        'events' => [],
                        'type' => WebhookTypes::TRANSACTIONAL->value,
                        'createdAt' => '',
                        'modifiedAt' => '',
                    ],
                ],
            ], Response::HTTP_OK),
        ]);

        $fetchAllWebhookRequest = app(FetchAllWebhookRequest::class);

        $this->assertInstanceOf(FetchAllWebhookResponse::class, $fetchAllWebhookRequest->send(WebhookTypes::TRANSACTIONAL->value));
    }

    /** @test */
    public function send_bad()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url').'webhooks*' => Http::response(
                ['message' => 'invalid', 'code' => Response::HTTP_BAD_REQUEST],
                Response::HTTP_BAD_REQUEST
            ),
        ]);

        $fetchAllWebhookRequest = app(FetchAllWebhookRequest::class);

        $this->assertInstanceOf(BadResponse::class, $fetchAllWebhookRequest->send(WebhookTypes::TRANSACTIONAL->value));
    }
}
