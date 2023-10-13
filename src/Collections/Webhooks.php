<?php

namespace Jcergolj\BrevoWebhookManager\Collections;

use Illuminate\Support\Collection;
use Jcergolj\BrevoWebhookManager\Items\Webhook;

class Webhooks extends Collection
{
    public function __construct($items)
    {
        $items = collect($items)->map(function ($item) {
            return Webhook::fromArray($item);
        });

        parent::__construct($items);
    }

    public function itemsValueToString()
    {
        return collect($this->items)->map(function ($webhook) {
            return [
                'id' => $webhook->id,
                'url' => $webhook->url,
                'description' => $webhook->description,
                'type' => $webhook->type->value,
                'events' => $webhook->events->implode(),
                'createdAt' => $webhook->createdAt->format('d/m/Y H:i:s'),
                'modifiedAt' => $webhook->modifiedAt->format('d/m/Y H:i:s'),
                'domain' => $webhook->domain ?? 'N/A',
            ];
        })->toArray();
    }
}
