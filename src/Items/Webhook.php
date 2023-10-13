<?php

namespace Jcergolj\BrevoWebhookManager\Items;

use Illuminate\Support\Carbon;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\ValueObjects\WebhookEvents;

class Webhook
{
    private function __construct(
        public int $id,
        public string $url,
        public string $description,
        public WebhookEvents $events,
        public WebhookTypes $type,
        public Carbon $createdAt,
        public Carbon $modifiedAt,
        public ?string $domain = null
    ) {
    }

    public static function fromArray(array $attributes): Webhook
    {
        return new self(
            $attributes['id'],
            $attributes['url'],
            $attributes['description'],
            new WebhookEvents($attributes['type'], $attributes['events']),
            WebhookTypes::from($attributes['type']),
            Carbon::parse($attributes['createdAt']),
            Carbon::parse($attributes['modifiedAt']),
            $attributes['domain'] ?? null,
        );
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'description' => $this->description,
            'events' => $this->events,
            'type' => $this->type,
            'createdAt' => $this->createdAt,
            'modifiedAt' => $this->modifiedAt,
            'domain' => $this->domain,
        ];
    }
}
