<?php

namespace Jcergolj\BrevoWebhookManager\Console\Commands;

use Illuminate\Console\Command;
use Jcergolj\BrevoWebhookManager\Enums\InboundWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes\UpdateAttribute;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\FetchWebhookRequest;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\UpdateWebhookRequest;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class UpdateWebhook extends Command
{
    protected $signature = 'brevo-webhooks:update-webhook';

    protected $description = 'Update webhook';

    public function handle(UpdateWebhookRequest $updateRequest, FetchWebhookRequest $fetchRequest): void
    {
        $id = text('Please provide the webhook\'s id:', required: true);

        $response = spin(
            function () use ($fetchRequest, $id) {
                return $fetchRequest->send((int) $id);
            },
            'Fetching response...'
        );

        if ($response->bad()) {
            $this->error('Failed to fetch the webhook');

            return;
        }

        $url = text('Please provide URL of the webhook:', required: true);
        $description = text('Please provide webhook description:');

        if ($response->webhook->type === WebhookTypes::INBOUND) {
            $attributes = $this->inboundWebhookAttributes($url, $description);
        } else {
            $attributes = $this->marketingOrTransactionalWebhookAttributes($url, $description, $response->webhook->type);
        }

        $response = spin(
            function () use ($updateRequest, $id, $attributes) {
                return $updateRequest->send((int) $id, $attributes);
            },
            'Fetching response...'
        );

        if ($response->success()) {
            $this->info('Webhook updated successfully');

            return;
        }

        $this->error('Webhook was not updated: '.$response->error);
    }

    protected function getEvents(WebhookTypes $type): array
    {
        if ($type === WebhookTypes::TRANSACTIONAL) {
            return TransactionalWebhookEvents::arrayOfValues();
        }

        return MarketingWebhookEvents::arrayOfValues();
    }

    protected function inboundWebhookAttributes(string $url, string $description)
    {
        $domain = text('Please provide domain for inbound webhook:', required: true);

        return new UpdateAttribute(
            $url,
            $description,
            InboundWebhookEvents::arrayOfValues(),
            WebhookTypes::INBOUND,
            $domain
        );
    }

    protected function marketingOrTransactionalWebhookAttributes(string $url, string $description, WebhookTypes $type)
    {
        $events = multiselect(
            label: 'Select webhook events:',
            options: $this->getEvents($type),
            required: true
        );

        return new UpdateAttribute(
            $url,
            $description,
            $events,
            $type
        );
    }
}
