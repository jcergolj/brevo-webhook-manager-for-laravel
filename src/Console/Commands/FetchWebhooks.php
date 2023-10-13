<?php

namespace Jcergolj\BrevoWebhookManager\Console\Commands;

use Illuminate\Console\Command;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\FetchAllWebhookRequest;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\select;

class FetchWebhooks extends Command
{
    protected $signature = 'brevo-webhooks:fetch-all';

    protected $description = 'Fetch all webhooks';

    public function handle(FetchAllWebhookRequest $fetchRequest): void
    {
        $type = select(
            label: 'Select webhook events:',
            options: WebhookTypes::arrayOfValues(),
            required: true
        );

        $response = spin(
            function () use ($fetchRequest, $type) {
                return $fetchRequest->send($type);
            },
            'Fetching response...'
        );

        if ($response->bad()) {
            $this->error('Failed to fetch webhooks: '.$response->error);

            return;
        }

        $webhooks = $response->webhooks->itemsValueToString();

        table(
            ['ID', 'Url', 'Description', 'Type', 'Events', 'Created At', 'Modified At', 'Domain'],
            $webhooks
        );
    }
}
