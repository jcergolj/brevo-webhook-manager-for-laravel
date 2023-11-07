<?php

namespace Jcergolj\BrevoWebhookManager\Console\Commands;

use Illuminate\Console\Command;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\FetchWebhookRequest;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class FetchWebhook extends Command
{
    protected $signature = 'brevo-webhooks:fetch';

    protected $description = 'Fetch webhook';

    public function handle(FetchWebhookRequest $fetchRequest): void
    {
        $id = text('Please provide the webhook\'s id:', required: true);

        $response = spin(
            function () use ($fetchRequest, $id) {
                return $fetchRequest->send((int) $id);
            },
            'Fetching response...'
        );

        if ($response->bad()) {
            $this->error('Failed to fetch webhook: '.$response->error);

            return;
        }

        table(
            ['Url', 'Description', 'Type', 'Events', 'Created At', 'Modified At', 'Domain'],
            [
                [
                    $response->webhook->id,
                    $response->webhook->url,
                    $response->webhook->description,
                    $response->webhook->type->value,
                    $response->webhook->events->implode(),
                    $response->webhook->createdAt->format('d/m/Y H:i:s'),
                    $response->webhook->modifiedAt->format('d/m/Y H:i:s'),
                    $response->webhook->domain ?? 'N/A',
                ],
            ]
        );
    }
}
