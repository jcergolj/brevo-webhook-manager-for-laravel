<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class FetchWebhooksTest extends TestCase
{
    /** @var array */
    public $webhooks;

    public function setUp(): void
    {
        parent::setUp();

        $this->webhooks = [
            [
                'id' => 123,
                'type' => WebhookTypes::MARKETING->value,
                'url' => 'http://example.com',
                'description' => 'example webhook',
                'events' => [
                    MarketingWebhookEvents::SOFT_BOUNCE->value,
                ],
                'createdAt' => Carbon::now()->toDateTimeString(),
                'modifiedAt' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 456,
                'type' => WebhookTypes::MARKETING->value,
                'url' => 'http://example.com',
                'description' => 'example webhook',
                'events' => [
                    MarketingWebhookEvents::SOFT_BOUNCE->value,
                ],
                'createdAt' => Carbon::now()->toDateTimeString(),
                'modifiedAt' => Carbon::now()->toDateTimeString(),
            ],
        ];
    }

    /** @test */
    public function fetch_webhooks()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url').'webhooks*' => Http::response(
                ['webhooks' => $this->webhooks],
                Response::HTTP_OK
            ),
        ]);

        $this->webhooks[0]['events'] = implode(', ', $this->webhooks[0]['events']);
        $this->webhooks[1]['events'] = implode(', ', $this->webhooks[1]['events']);

        $this->artisan('brevo-webhooks:fetch-all')
            ->expectsQuestion(
                'Select webhook events:',
                WebhookTypes::MARKETING->value
            )
            ->expectsTable(
                ['Url', 'Description', 'Type', 'Events', 'Created At', 'Modified At', 'Domain'],
                $this->webhooks
            );

        Http::assertSentInOrder([
            $this->assertFetchAllWebhookRequest(),
        ]);
    }

    /** @test */
    public function webhooks_are_not_fetched()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url').'webhooks*' => Http::response(
                ['message' => 'Invalid', 'code' => Response::HTTP_BAD_REQUEST],
                Response::HTTP_BAD_REQUEST
            ),
        ]);

        $this->artisan('brevo-webhooks:fetch-all')
            ->expectsQuestion(
                'Select webhook events:',
                WebhookTypes::MARKETING->value
            )
            ->expectsOutput('Failed to fetch webhooks: Invalid');

        Http::assertSentInOrder([
            $this->assertFetchAllWebhookRequest(),
        ]);
    }

    protected function assertFetchAllWebhookRequest()
    {
        return function (Request $request) {
            $this->assertSame(
                config('brevo-webhook-manager.brevo.base_url').'webhooks?type=marketing',
                $request->url()
            );

            $this->assertSame('GET', $request->method());

            $this->assertSame(WebhookTypes::MARKETING->value, $request->data()['type']);

            return true;
        };
    }
}
