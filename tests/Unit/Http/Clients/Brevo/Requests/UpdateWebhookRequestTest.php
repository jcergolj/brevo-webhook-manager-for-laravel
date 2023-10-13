<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Brevo;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes\CreateAttribute;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes\UpdateAttribute;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\UpdateWebhookRequest;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\UpdateWebhookResponse;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class UpdateWebhookRequestTest extends TestCase
{
    /** @var int */
    public $webhookId = 123;

    /** @var CreateAttribute */
    public $attributes;

    public function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        $this->attributes = new UpdateAttribute(
            'https://example.com',
            'Example webhook',
            [TransactionalWebhookEvents::DELIVERED->value],
            WebhookTypes::TRANSACTIONAL
        );
    }

    /** @test */
    public function send_successful()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::response([], Response::HTTP_NO_CONTENT),
        ]);

        $updateWebhookRequest = app(UpdateWebhookRequest::class);

        $this->assertInstanceOf(UpdateWebhookResponse::class, $updateWebhookRequest->send($this->webhookId, $this->attributes));
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

        $updateWebhookRequest = app(UpdateWebhookRequest::class);

        $this->assertInstanceOf(BadResponse::class, $updateWebhookRequest->send($this->webhookId, $this->attributes));
    }
}
