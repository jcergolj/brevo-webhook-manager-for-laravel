<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Enums\InboundWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class UpdateWebhookTest extends TestCase
{
    /** @var int */
    public $webhookId = 123;

    /** @var string */
    public $toUpdateUrl = 'https://updated-example.com';

    /** @var string */
    public $toUpdateDescription = 'updated example description';

    /** @var string */
    public $toUpdateEvent;

    /** @var string */
    public $toUpdateDomain = 'updated-example.com';

    public function setUp(): void
    {
        parent::setUp();

        // both has click event
        $this->toUpdateEvent = MarketingWebhookEvents::CLICK->value;
    }

    /** @test */
    public function marketing_webhook_is_updated()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::sequence()
                ->push(
                    [
                        'id' => $this->webhookId,
                        'type' => WebhookTypes::MARKETING->value,
                        'url' => 'http://example.com',
                        'description' => 'example webhook',
                        'events' => [
                            MarketingWebhookEvents::SOFT_BOUNCE->value,
                        ],
                        'createdAt' => Carbon::now()->toDateTimeString(),
                        'modifiedAt' => Carbon::now()->toDateTimeString(),
                    ],
                    Response::HTTP_OK
                )->push(
                    [],
                    Response::HTTP_NO_CONTENT
                ),
        ]);

        $this->artisan('brevo-webhooks:update-webhook')
            ->expectsQuestion('Please provide the webhook\'s id:', $this->webhookId)
            ->expectsQuestion('Please provide URL of the webhook:', $this->toUpdateUrl)
            ->expectsQuestion('Please provide webhook description:', $this->toUpdateDescription)
            ->expectsQuestion(
                'Select webhook events:',
                $this->toUpdateEvent
            )
            ->expectsOutput('Webhook updated successfully');

        Http::assertSentInOrder([
            $this->assertFetchWebhookRequest(),
            $this->assertUpdateWebhookRequest(),
        ]);
    }

    /** @test */
    public function transactional_webhook_is_updated()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::sequence()
                ->push(
                    [
                        'id' => $this->webhookId,
                        'type' => WebhookTypes::TRANSACTIONAL->value,
                        'url' => 'http://example.com',
                        'description' => 'example webhook',
                        'events' => [
                            TransactionalWebhookEvents::SOFT_BOUNCE->value,
                        ],
                        'createdAt' => Carbon::now()->toDateTimeString(),
                        'modifiedAt' => Carbon::now()->toDateTimeString(),
                    ],
                    Response::HTTP_OK
                )->push(
                    [],
                    Response::HTTP_NO_CONTENT
                ),
        ]);

        $this->artisan('brevo-webhooks:update-webhook')
            ->expectsQuestion('Please provide the webhook\'s id:', $this->webhookId)
            ->expectsQuestion('Please provide URL of the webhook:', $this->toUpdateUrl)
            ->expectsQuestion('Please provide webhook description:', $this->toUpdateDescription)
            ->expectsQuestion(
                'Select webhook events:',
                $this->toUpdateEvent
            )
            ->expectsOutput('Webhook updated successfully');

        Http::assertSentInOrder([
            $this->assertFetchWebhookRequest(),
            $this->assertUpdateWebhookRequest(),
        ]);
    }

    /** @test */
    public function inbound_webhook_is_updated()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::sequence()
                ->push(
                    [
                        'id' => $this->webhookId,
                        'type' => WebhookTypes::INBOUND->value,
                        'url' => 'http://example.com',
                        'description' => 'example webhook',
                        'events' => [
                            InboundWebhookEvents::INBOUND_EMAIL_PROCESSED->value,
                        ],
                        'createdAt' => Carbon::now()->toDateTimeString(),
                        'modifiedAt' => Carbon::now()->toDateTimeString(),
                    ],
                    Response::HTTP_OK
                )->push(
                    [],
                    Response::HTTP_NO_CONTENT
                ),
        ]);

        $this->artisan('brevo-webhooks:update-webhook')
            ->expectsQuestion('Please provide the webhook\'s id:', $this->webhookId)
            ->expectsQuestion('Please provide URL of the webhook:', $this->toUpdateUrl)
            ->expectsQuestion('Please provide webhook description:', $this->toUpdateDescription)
            ->expectsQuestion('Please provide domain for inbound webhook:', $this->toUpdateDomain)
            ->expectsOutput('Webhook updated successfully');

        Http::assertSentInOrder([
            $this->assertFetchWebhookRequest(),
            $this->assertUpdateInboundWebhookRequest(),
        ]);
    }

    /** @test */
    public function webhook_is_not_updated()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::sequence()
                ->push(
                    [
                        'id' => $this->webhookId,
                        'type' => WebhookTypes::TRANSACTIONAL->value,
                        'url' => 'http://example.com',
                        'description' => 'example webhook',
                        'events' => [
                            TransactionalWebhookEvents::SOFT_BOUNCE->value,
                        ],
                        'createdAt' => Carbon::now()->toDateTimeString(),
                        'modifiedAt' => Carbon::now()->toDateTimeString(),
                    ],
                    Response::HTTP_OK
                )->push(
                    ['message' => 'Invalid', 'code' => Response::HTTP_BAD_REQUEST],
                    Response::HTTP_BAD_REQUEST
                ),
        ]);

        $this->artisan('brevo-webhooks:update-webhook')
            ->expectsQuestion('Please provide the webhook\'s id:', $this->webhookId)
            ->expectsQuestion('Please provide URL of the webhook:', $this->toUpdateUrl)
            ->expectsQuestion('Please provide webhook description:', $this->toUpdateDescription)
            ->expectsQuestion(
                'Select webhook events:',
                $this->toUpdateEvent
            )
            ->expectsOutput('Webhook was not updated: Invalid');

        Http::assertSentInOrder([
            $this->assertFetchWebhookRequest(),
            $this->assertUpdateWebhookRequest(),
        ]);
    }

    /** @test */
    public function webhook_is_not_fetched()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::response(
                ['message' => 'Invalid', 'code' => Response::HTTP_BAD_REQUEST],
                Response::HTTP_BAD_REQUEST
            ),
        ]);

        $this->artisan('brevo-webhooks:update-webhook')
            ->expectsQuestion('Please provide the webhook\'s id:', $this->webhookId)
            ->expectsOutput('Failed to fetch the webhook');

        Http::assertSentInOrder([
            $this->assertFetchWebhookRequest(),
        ]);
    }

    protected function assertFetchWebhookRequest()
    {
        return function (Request $request) {
            $this->assertSame(
                config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}",
                $request->url()
            );

            $this->assertSame('GET', $request->method());

            $this->assertSame([], $request->data());

            return true;
        };
    }

    protected function assertUpdateWebhookRequest()
    {
        return function (Request $request) {
            $this->assertSame(
                config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}",
                $request->url()
            );

            $this->assertSame('PUT', $request->method());

            $this->assertSame($this->toUpdateUrl, $request->data()['url']);

            $this->assertCount(1, $request->data()['events']);
            $this->assertSame($this->toUpdateEvent, $request->data()['events'][0]);

            $this->assertSame($this->toUpdateDescription, $request->data()['description']);

            return true;
        };
    }

    protected function assertUpdateInboundWebhookRequest()
    {
        return function (Request $request) {
            $this->assertSame(
                config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}",
                $request->url()
            );

            $this->assertSame('PUT', $request->method());

            $this->assertSame($this->toUpdateUrl, $request->data()['url']);

            $this->assertSame($this->toUpdateDescription, $request->data()['description']);

            $this->assertSame($this->toUpdateDomain, $request->data()['domain']);

            return true;
        };
    }
}
