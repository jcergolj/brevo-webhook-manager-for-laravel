<?php

namespace Jcergolj\BrevoWebhookManager\Console\Commands;

use Illuminate\Console\Command;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes\CreateAttribute;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\CreateWebhookRequest;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class CreateTransactionalWebhook extends Command
{
    protected $signature = 'brevo-webhooks:create-transactional';

    protected $description = 'Create a transactional webhook';

    public function handle(CreateWebhookRequest $request): void
    {
        $url = text('Please provide URL of the webhook:', required: true);
        $description = text('Please provide webhook description:');

        $events = multiselect(
            label: 'Select webhook events:',
            options: TransactionalWebhookEvents::arrayOfValues(),
            required: true
        );

        $attributes = new CreateAttribute(
            $url,
            $description,
            $events,
            WebhookTypes::TRANSACTIONAL
        );

        $response = spin(
            function () use ($request, $attributes) {
                return $request->send($attributes);
            },
            'Fetching response...'
        );

        if ($response->success()) {
            $this->info('Webhook created successfully');

            return;
        }

        $this->error('Webhook was not created: '.$response->error);
    }
}
