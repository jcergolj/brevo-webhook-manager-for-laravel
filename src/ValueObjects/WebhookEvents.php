<?php

namespace Jcergolj\BrevoWebhookManager\ValueObjects;

use Jcergolj\BrevoWebhookManager\Enums\InboundWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;

class WebhookEvents
{
    /** @var array */
    public $items = [];

    public function __construct(public string $type, public array $events)
    {
        $this->items = $this->toWebhookEvents();
    }

    public function implode()
    {
        return collect($this->items)->map(function ($event) {
            return $event->value;
        })->implode(', ');
    }

    private function toWebhookEvents(): array
    {
        if ($this->type === WebhookTypes::TRANSACTIONAL->value) {
            return TransactionalWebhookEvents::fromStringArray($this->events);
        }

        if ($this->type === WebhookTypes::MARKETING->value) {
            return MarketingWebhookEvents::fromStringArray($this->events);
        }

        if ($this->type === WebhookTypes::INBOUND->value) {
            return InboundWebhookEvents::fromStringArray($this->events);
        }

        return [];
    }
}
