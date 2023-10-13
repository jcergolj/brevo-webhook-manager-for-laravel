<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\ValueObjects;

use Jcergolj\BrevoWebhookManager\Enums\InboundWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;
use Jcergolj\BrevoWebhookManager\ValueObjects\WebhookEvents;

class WebhookEventsTest extends TestCase
{
    /** @test */
    public function transactional_type_events()
    {
        $webhookEvents = new WebhookEvents(WebhookTypes::TRANSACTIONAL->value, [TransactionalWebhookEvents::BLOCKED->value, TransactionalWebhookEvents::CLICK->value]);

        $this->assertSame([TransactionalWebhookEvents::BLOCKED, TransactionalWebhookEvents::CLICK], $webhookEvents->items);
    }

    /** @test */
    public function marketing_type_events()
    {
        $webhookEvents = new WebhookEvents(WebhookTypes::MARKETING->value, [MarketingWebhookEvents::CLICK->value, MarketingWebhookEvents::DELIVERED->value]);

        $this->assertSame([MarketingWebhookEvents::CLICK, MarketingWebhookEvents::DELIVERED], $webhookEvents->items);
    }

    /** @test */
    public function inbound_type_events()
    {
        $webhookEvents = new WebhookEvents(WebhookTypes::INBOUND->value, [InboundWebhookEvents::INBOUND_EMAIL_PROCESSED->value]);

        $this->assertSame([InboundWebhookEvents::INBOUND_EMAIL_PROCESSED], $webhookEvents->items);
    }

    /** @test */
    public function implode()
    {
        $webhookEvents = new WebhookEvents(WebhookTypes::MARKETING->value, [MarketingWebhookEvents::CLICK->value, MarketingWebhookEvents::DELIVERED->value]);

        $this->assertSame('click, delivered', $webhookEvents->implode());

    }
}
