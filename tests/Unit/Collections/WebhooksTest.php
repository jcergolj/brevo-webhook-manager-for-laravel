<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Responses;

use Illuminate\Support\Carbon;
use Jcergolj\BrevoWebhookManager\Collections\Webhooks;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class WebhooksTest extends TestCase
{
    /** @var array */
    public $attributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->attributes = [
            [
                'id' => 123,
                'url' => 'https://example.com',
                'description' => 'webhook description',
                'events' => [TransactionalWebhookEvents::CLICK->value],
                'type' => WebhookTypes::TRANSACTIONAL->value,
                'createdAt' => '2022-01-01T00:00:00Z',
                'modifiedAt' => '2022-01-01T00:00:00Z',
            ],
            [
                'id' => 456,
                'url' => 'https://test.com',
                'description' => 'test description',
                'events' => [MarketingWebhookEvents::DELIVERED->value],
                'type' => WebhookTypes::MARKETING->value,
                'createdAt' => '2023-01-01T00:00:00Z',
                'modifiedAt' => '2023-01-01T00:00:00Z',
            ],
        ];
    }

    /** @test */
    public function items_value_to_string()
    {
        $webhooks = new Webhooks($this->attributes);

        $webhooksArray = $webhooks->itemsValueToString();
        $firstWebhook = $webhooksArray[0];

        $this->assertEquals($this->attributes[0]['id'], $firstWebhook['id']);

        $this->assertEquals($this->attributes[0]['url'], $firstWebhook['url']);

        $this->assertEquals($this->attributes[0]['description'], $firstWebhook['description']);

        $this->assertEquals(WebhookTypes::TRANSACTIONAL->value, $firstWebhook['type']);

        $this->assertEquals(
            Carbon::parse($this->attributes[0]['createdAt'])->format('d/m/Y H:i:s'),
            $firstWebhook['createdAt']
        );

        $this->assertEquals(
            Carbon::parse($this->attributes[0]['modifiedAt'])->format('d/m/Y H:i:s'),
            $firstWebhook['modifiedAt']
        );

        $secondWebhook = $webhooksArray[1];

        $this->assertEquals($this->attributes[1]['id'], $secondWebhook['id']);

        $this->assertEquals($this->attributes[1]['url'], $secondWebhook['url']);

        $this->assertEquals($this->attributes[1]['description'], $secondWebhook['description']);

        $this->assertEquals(WebhookTypes::MARKETING->value, $secondWebhook['type']);

        $this->assertEquals(
            Carbon::parse($this->attributes[1]['createdAt'])->format('d/m/Y H:i:s'),
            $secondWebhook['createdAt']
        );

        $this->assertEquals(
            Carbon::parse($this->attributes[1]['modifiedAt'])->format('d/m/Y H:i:s'),
            $secondWebhook['modifiedAt']
        );
    }
}
