<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Brevo;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\DeleteWebhookRequest;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\DeleteWebhookResponse;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class DeleteWebhookRequestTest extends TestCase
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
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::response([], Response::HTTP_NO_CONTENT),
        ]);

        $deleteWebhookRequest = app(DeleteWebhookRequest::class);

        $this->assertInstanceOf(DeleteWebhookResponse::class, $deleteWebhookRequest->send($this->webhookId));
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

        $deleteWebhookRequest = app(DeleteWebhookRequest::class);

        $this->assertInstanceOf(BadResponse::class, $deleteWebhookRequest->send($this->webhookId));
    }
}
