<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Jcergolj\BrevoWebhookManager\Enums\InboundWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Exceptions\InvalidUrl;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Exceptions\InvalidWebhookType;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes\UpdateAttribute;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class UpdateAttributeTest extends TestCase
{
    /** @test */
    public function to_array()
    {
        $attribute = new UpdateAttribute(
            'https://example.com',
            'Example webhook',
            [TransactionalWebhookEvents::DELIVERED->value],
            WebhookTypes::TRANSACTIONAL
        );

        $attributes = $attribute->toArray();

        $this->assertSame('https://example.com', $attributes['url']);

        $this->assertSame([TransactionalWebhookEvents::DELIVERED->value], $attributes['events']);

        $this->assertSame('Example webhook', $attributes['description']);
    }

    /** @test */
    public function to_array_no_description()
    {
        $attribute = new UpdateAttribute(
            'https://example.com',
            '',
            [TransactionalWebhookEvents::DELIVERED->value],
            WebhookTypes::TRANSACTIONAL
        );

        $attributes = $attribute->toArray();

        $this->assertSame('https://example.com', $attributes['url']);

        $this->assertSame([TransactionalWebhookEvents::DELIVERED->value], $attributes['events']);

        $this->assertArrayNotHasKey('description', $attributes);
    }

    /** @test */
    public function to_array_domain()
    {
        $attribute = new UpdateAttribute(
            'https://example.com',
            'Example webhook',
            [InboundWebhookEvents::INBOUND_EMAIL_PROCESSED->value],
            WebhookTypes::INBOUND,
            'example.com'
        );

        $attributes = $attribute->toArray();

        $this->assertSame('https://example.com', $attributes['url']);

        $this->assertSame([InboundWebhookEvents::INBOUND_EMAIL_PROCESSED->value], $attributes['events']);

        $this->assertSame('Example webhook', $attributes['description']);

        $this->assertSame('example.com', $attributes['domain']);
    }

    /** @test */
    public function invalid_url()
    {
        $this->expectException(InvalidUrl::class);

        new UpdateAttribute(
            'example',
            'Example webhook',
            [TransactionalWebhookEvents::DELIVERED->value],
            WebhookTypes::TRANSACTIONAL,
        );
    }

    /** @test */
    public function invalid_domain_inbound_webhook_type_domain_null()
    {
        $this->expectException(InvalidWebhookType::class);

        new UpdateAttribute(
            'https://example.com',
            'Example webhook',
            [TransactionalWebhookEvents::DELIVERED->value],
            WebhookTypes::INBOUND,
        );
    }

    /** @test */
    public function invalid_domain_not_inbound_webhook_type_domain_not_null()
    {
        $this->expectException(InvalidWebhookType::class);

        new UpdateAttribute(
            'https://example.com',
            'Example webhook',
            [TransactionalWebhookEvents::DELIVERED->value],
            WebhookTypes::TRANSACTIONAL,
            'example.com'
        );
    }

    /** @test */
    public function invalid_event_for_transactional_webhook()
    {
        $this->expectException(InvalidWebhookType::class);

        new UpdateAttribute(
            'https://example.com',
            'Example webhook',
            ['non-existing-event'],
            WebhookTypes::TRANSACTIONAL
        );
    }

    /** @test */
    public function valid_event_for_transactional_webhook()
    {
        try {
            foreach (TransactionalWebhookEvents::arrayOfValues() as $event) {
                new UpdateAttribute(
                    'https://example.com',
                    'Example webhook',
                    [$event],
                    WebhookTypes::TRANSACTIONAL
                );
            }
        } catch (InvalidWebhookType $exception) {
            $this->fail('Valid event for transactional webhook type was market as invalid.');
        }

        $this->assertTrue(true);
    }

    /** @test */
    public function invalid_event_for_marketing_webhook()
    {
        $this->expectException(InvalidWebhookType::class);

        new UpdateAttribute(
            'https://example.com',
            'Example webhook',
            ['non-existing-event'],
            WebhookTypes::MARKETING
        );
    }

    /** @test */
    public function valid_event_for_marketing_webhook()
    {
        try {
            foreach (MarketingWebhookEvents::arrayOfValues() as $event) {
                new UpdateAttribute(
                    'https://example.com',
                    'Example webhook',
                    [$event],
                    WebhookTypes::MARKETING
                );
            }
        } catch (InvalidWebhookType $exception) {
            $this->fail('Valid event for marketing webhook type was market as invalid.');
        }

        $this->assertTrue(true);
    }

    /** @test */
    public function invalid_event_for_inbound_webhook()
    {
        $this->expectException(InvalidWebhookType::class);

        new UpdateAttribute(
            'https://example.com',
            'Example webhook',
            ['non-existing-event'],
            WebhookTypes::INBOUND
        );
    }

    /** @test */
    public function valid_event_for_inbound_webhook()
    {
        try {
            foreach (InboundWebhookEvents::arrayOfValues() as $event) {
                new UpdateAttribute(
                    'https://example.com',
                    'Example webhook',
                    [$event],
                    WebhookTypes::INBOUND,
                    'example.com'
                );
            }
        } catch (InvalidWebhookType $exception) {
            $this->fail('Valid event for inbound webhook type was market as invalid.');
        }

        $this->assertTrue(true);
    }
}
