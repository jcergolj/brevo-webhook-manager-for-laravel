<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes;

use Illuminate\Support\Facades\Validator;
use Jcergolj\BrevoWebhookManager\Enums\InboundWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Exceptions\InvalidUrl;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Exceptions\InvalidWebhookType;

class CreateAttribute
{
    public function __construct(
        protected string $url,
        protected string $description,
        protected array $events,
        protected WebhookTypes $type,
        protected ?string $domain = null,
    ) {
        $this->validateUrl();

        $this->validateDomain();

        $this->validateEvents();
    }

    public function toArray(): array
    {
        $attributes = [
            'url' => $this->url,
            'events' => $this->events,
            'type' => $this->type->value,
        ];

        if ($this->description !== '') {
            $attributes['description'] = $this->description;
        }

        if (isset($this->domain)) {
            $attributes['domain'] = $this->domain;
        }

        return $attributes;
    }

    /** @throws InvalidUrl */
    protected function validateUrl(): void
    {
        $validator = Validator::make(['url' => $this->url], ['url' => 'url']);

        if ($validator->fails()) {
            throw new InvalidUrl('Invalid url format');
        }
    }

    /** @throws InvalidWebhookType */
    protected function validateDomain(): void
    {
        if ($this->domain === null && $this->type === WebhookTypes::INBOUND) {
            throw new InvalidWebhookType('Inbound webhooks should have domain set.');
        }

        if ($this->domain !== null && $this->type !== WebhookTypes::INBOUND) {
            throw new InvalidWebhookType('Domain shouldn\'t be set for non Inbound webhooks.');
        }
    }

    protected function validateEvents(): void
    {
        if ($this->type === WebhookTypes::TRANSACTIONAL) {
            $this->validateTransactionalEvents();
        }

        if ($this->type === WebhookTypes::MARKETING) {
            $this->validateMarketingEvents();
        }

        if ($this->type === WebhookTypes::INBOUND) {
            $this->validateInboundEvents();
        }
    }

    /** @throws InvalidWebhookType */
    private function validateTransactionalEvents(): void
    {
        collect($this->events)->each(function ($event) {
            $validEvents = TransactionalWebhookEvents::arrayOfValues();
            throw_if(! in_array($event, $validEvents), new InvalidWebhookType('Invalid event webhook combination.'));
        });
    }

    /** @throws InvalidWebhookType */
    private function validateMarketingEvents(): void
    {
        collect($this->events)->each(function ($event) {
            $validEvents = MarketingWebhookEvents::arrayOfValues();
            throw_if(! in_array($event, $validEvents), new InvalidWebhookType('Invalid event webhook combination.'));
        });
    }

    /** @throws InvalidWebhookType */
    private function validateInboundEvents()
    {
        collect($this->events)->each(function ($event) {
            $validEvents = InboundWebhookEvents::arrayOfValues();
            throw_if(! in_array($event, $validEvents), new InvalidWebhookType('Invalid event webhook combination.'));
        });
    }
}
