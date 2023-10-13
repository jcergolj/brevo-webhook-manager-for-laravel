<?php

namespace Jcergolj\BrevoWebhookManager\Console\Commands;

use Illuminate\Console\Command;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Requests\DeleteWebhookRequest;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class DeleteWebhook extends Command
{
    protected $signature = 'brevo-webhooks:delete';

    protected $description = 'Delete webhook';

    public function handle(DeleteWebhookRequest $request): void
    {
        $id = text('Please provide the webhook\'s id:', required: true);

        $response = spin(
            function () use ($request, $id) {
                return $request->send((int) $id);
            },
            'Fetching response...'
        );

        if ($response->success()) {
            $this->info('Webhook deleted successfully');

            return;
        }

        $this->error('Webhook was not deleted: '.$response->error);
    }
}
