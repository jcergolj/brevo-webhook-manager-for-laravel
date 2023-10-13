<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Brevo;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\FetchWebhookRequest;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\FetchWebhookResponse;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class FetchWebhookRequestTest extends TestCase
{
    /** @var int */
    public $webhookId = 123;

    public function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    /** @test */
    public function send_successful()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::response(
                [
                    'id' => $this->webhookId,
                    'url' => '',
                    'description' => '',
                    'events' => [],
                    'type' => WebhookTypes::TRANSACTIONAL->value,
                    'createdAt' => '',
                    'modifiedAt' => '',
                ],
                Response::HTTP_OK
            ),
        ]);

        $fetchWebhookRequest = app(FetchWebhookRequest::class);

        $this->assertInstanceOf(FetchWebhookResponse::class, $fetchWebhookRequest->send($this->webhookId));
    }

    /** @test */
    public function send_bad()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::response(
                ['message' => 'invalid', 'code' => Response::HTTP_BAD_REQUEST],
                Response::HTTP_BAD_REQUEST
            ),
        ]);

        $fetchWebhookRequest = app(FetchWebhookRequest::class);

        $this->assertInstanceOf(BadResponse::class, $fetchWebhookRequest->send($this->webhookId));
    }
}
