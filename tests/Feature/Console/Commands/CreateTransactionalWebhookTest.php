<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class CreateTransactionalWebhookTest extends TestCase
{
    /** @var int */
    public $webhookId = 123;

    /** @test */
    public function webhook_is_created()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url').'webhooks' => Http::response(['id' => $this->webhookId], Response::HTTP_CREATED),
        ]);

        $this->artisan('brevo-webhooks:create-transactional')
            ->expectsQuestion('Please provide URL of the webhook:', 'https://example.com')
            ->expectsQuestion('Please provide webhook description:', 'example webhook')
            ->expectsQuestion(
                'Select webhook events:',
                TransactionalWebhookEvents::SENT->value,
            )->expectsOutput('Webhook created successfully');

        Http::assertSentInOrder([function (Request $request) {
            $this->assertSame('POST', $request->method());

            $this->assertSame(config('brevo-webhook-manager.brevo.base_url').'webhooks', $request->url());

            $this->assertSame('https://example.com', $request->data()['url']);

            $this->assertSame([TransactionalWebhookEvents::SENT->value], $request->data()['events']);

            $this->assertSame(WebhookTypes::TRANSACTIONAL->value, $request->data()['type']);

            $this->assertSame('example webhook', $request->data()['description']);

            return true;
        }]);
    }

    /** @test */
    public function webhook_is_not_created()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url').'webhooks' => Http::response(
                ['message' => 'Invalid', 'code' => Response::HTTP_BAD_REQUEST],
                Response::HTTP_BAD_REQUEST
            ),
        ]);

        $this->artisan('brevo-webhooks:create-transactional')
            ->expectsQuestion('Please provide URL of the webhook:', 'https://example.com')
            ->expectsQuestion('Please provide webhook description:', 'example webhook')
            ->expectsQuestion(
                'Select webhook events:',
                TransactionalWebhookEvents::SENT->value,
            )->expectsOutput('Webhook was not created: Invalid');

        Http::assertSentInOrder([function (Request $request) {
            $this->assertSame('POST', $request->method());

            $this->assertSame(config('brevo-webhook-manager.brevo.base_url').'webhooks', $request->url());

            $this->assertSame('https://example.com', $request->data()['url']);

            $this->assertSame([TransactionalWebhookEvents::SENT->value], $request->data()['events']);

            $this->assertSame(WebhookTypes::TRANSACTIONAL->value, $request->data()['type']);

            $this->assertSame('example webhook', $request->data()['description']);

            return true;
        }]);
    }
}
