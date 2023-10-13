<?php

namespace Jcergolj\BrevoWebhookManager\Console\Commands;

use Illuminate\Console\Command;
use Jcergolj\BrevoWebhookManager\Enums\InboundWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\RequestAttributes\CreateAttribute;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\CreateWebhookRequest;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class CreateInboundWebhook extends Command
{
    protected $signature = 'brevo-webhooks:create-inbound';

    protected $description = 'Create a inbound webhook';

    public function handle(CreateWebhookRequest $request): void
    {
        $url = text('Please provide URL of the webhook:', required: true);
        $description = text('Please provide webhook description:');

        $domain = text('Please provide domain for inbound webhook:', required: true);

        $attributes = new CreateAttribute(
            $url,
            $description,
            InboundWebhookEvents::arrayOfValues(),
            WebhookTypes::INBOUND,
            $domain
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
