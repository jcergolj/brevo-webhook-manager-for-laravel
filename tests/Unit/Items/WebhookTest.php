<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Responses;

use Illuminate\Support\Carbon;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Items\Webhook;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;
use Jcergolj\BrevoWebhookManager\ValueObjects\WebhookEvents;

class WebhookTest extends TestCase
{
    /** @var array */
    public $attributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->attributes = [
            'id' => 123,
            'url' => 'https://example.com',
            'description' => 'webhook description',
            'events' => [TransactionalWebhookEvents::BLOCKED->value, TransactionalWebhookEvents::CLICK->value],
            'type' => WebhookTypes::TRANSACTIONAL->value,
            'createdAt' => '2016-06-07T09:10:10Z',
            'modifiedAt' => '2016-06-07T09:10:10Z',
        ];
    }

    /** @test */
    public function from_array()
    {
        $webhook = Webhook::fromArray($this->attributes);

        $this->assertEquals($this->attributes['id'], $webhook->id);

        $this->assertEquals($this->attributes['url'], $webhook->url);

        $this->assertEquals($this->attributes['description'], $webhook->description);

        $this->assertInstanceOf(WebhookEvents::class, $webhook->events);

        $this->assertEquals(WebhookTypes::TRANSACTIONAL->value, $webhook->type->value);

        $this->assertEquals(
            Carbon::parse($this->attributes['createdAt'])->toDateTimeString(),
            $webhook->createdAt->toDateTimeString()
        );

        $this->assertEquals(
            Carbon::parse($this->attributes['modifiedAt'])->toDateTimeString(),
            $webhook->modifiedAt->toDateTimeString()
        );
    }

    /** @test */
    public function to_array()
    {
        $webhook = Webhook::fromArray($this->attributes);
        $convertedAttributes = $webhook->toArray();

        $this->assertEquals($this->attributes['id'], $convertedAttributes['id']);

        $this->assertEquals($this->attributes['url'], $convertedAttributes['url']);

        $this->assertEquals($this->attributes['description'], $convertedAttributes['description']);

        $this->assertInstanceOf(WebhookEvents::class, $convertedAttributes['events']);

        $this->assertEquals(WebhookTypes::TRANSACTIONAL, $convertedAttributes['type']);

        $this->assertEquals(
            Carbon::parse($this->attributes['createdAt'])->toDateTimeString(),
            $convertedAttributes['createdAt']
        );

        $this->assertEquals(
            Carbon::parse($this->attributes['modifiedAt'])->toDateTimeString(),
            $convertedAttributes['modifiedAt']
        );
    }
}
