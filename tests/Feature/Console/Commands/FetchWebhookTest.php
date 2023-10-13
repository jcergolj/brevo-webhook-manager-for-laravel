<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class FetchWebhookTest extends TestCase
{
    /** @var array */
    public $attributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->attributes = [
            'id' => 123,
            'type' => WebhookTypes::MARKETING->value,
            'url' => 'http://example.com',
            'description' => 'example webhook',
            'events' => [
                MarketingWebhookEvents::SOFT_BOUNCE->value,
            ],
            'createdAt' => Carbon::now()->toDateTimeString(),
            'modifiedAt' => Carbon::now()->toDateTimeString(),
        ];
    }

    /** @test */
    public function fetch_webhook()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->attributes['id']}" => Http::response(
                $this->attributes,
                Response::HTTP_OK
            ),
        ]);

        $this->artisan('brevo-webhooks:fetch')
            ->expectsQuestion('Please provide the webhook\'s id:', $this->attributes['id'])
            ->expectsTable(
                ['Url', 'Description', 'Type', 'Events', 'Created At', 'Modified At', 'Domain'],
                [
                    [
                        $this->attributes['id'],
                        $this->attributes['url'],
                        $this->attributes['description'],
                        WebhookTypes::MARKETING->value,
                        MarketingWebhookEvents::SOFT_BOUNCE->value,
                        Carbon::parse($this->attributes['createdAt'])->format('d/m/Y H:i:s'),
                        Carbon::parse($this->attributes['modifiedAt'])->format('d/m/Y H:i:s'),
                        'N/A',
                    ],
                ]
            );

        Http::assertSentInOrder([
            $this->assertFetchWebhookRequest(),
        ]);
    }

    /** @test */
    public function webhook_is_not_fetched()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->attributes['id']}" => Http::response(
                ['message' => 'Invalid', 'code' => Response::HTTP_BAD_REQUEST],
                Response::HTTP_BAD_REQUEST
            ),
        ]);

        $this->artisan('brevo-webhooks:fetch')
            ->expectsQuestion('Please provide the webhook\'s id:', $this->attributes['id'])
            ->expectsOutput('Failed to fetch webhook: Invalid');

        Http::assertSentInOrder([
            $this->assertFetchWebhookRequest(),
        ]);
    }

    protected function assertFetchWebhookRequest()
    {
        return function (Request $request) {
            $this->assertSame(
                config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->attributes['id']}",
                $request->url()
            );

            $this->assertSame('GET', $request->method());

            $this->assertSame([], $request->data());

            return true;
        };
    }
}
