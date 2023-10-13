<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Brevo;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes\CreateAttribute;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\CreateWebhookRequest;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\CreateWebhookResponse;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class CreateWebhookRequestTest extends TestCase
{
    /** @var CreateAttribute */
    public $attributes;

    public function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        $this->attributes = new CreateAttribute(
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
            config('brevo-webhook-manager.brevo.base_url').'webhooks' => Http::response(['id' => 123], Response::HTTP_CREATED),
        ]);

        $createWebhookRequest = app(CreateWebhookRequest::class);

        $this->assertInstanceOf(CreateWebhookResponse::class, $createWebhookRequest->send($this->attributes));
    }

    /** @test */
    public function send_bad()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url').'webhooks' => Http::response(
                ['message' => 'invalid', 'code' => Response::HTTP_BAD_REQUEST],
                Response::HTTP_BAD_REQUEST
            ),
        ]);

        $createWebhookRequest = app(CreateWebhookRequest::class);

        $this->assertInstanceOf(BadResponse::class, $createWebhookRequest->send($this->attributes));
    }
}
