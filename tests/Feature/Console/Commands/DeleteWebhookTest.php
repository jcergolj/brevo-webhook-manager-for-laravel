<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class DeleteWebhookTest extends TestCase
{
    /** @var int */
    public $webhookId = 123;

    /** @test */
    public function webhook_is_deleted()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::response([], Response::HTTP_NO_CONTENT),
        ]);

        $this->artisan('brevo-webhooks:delete')
            ->expectsQuestion('Please provide the webhook\'s id:', $this->webhookId)
            ->expectsOutput('Webhook deleted successfully');

        Http::assertSentInOrder([
            $this->assertDeleteWebhookRequest(),
        ]);
    }

    /** @test */
    public function webhook_is_not_deleted()
    {
        Http::fake([
            config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}" => Http::response(
                ['message' => 'Invalid', 'code' => Response::HTTP_BAD_REQUEST],
                Response::HTTP_BAD_REQUEST
            ),
        ]);

        $this->artisan('brevo-webhooks:delete')
            ->expectsQuestion('Please provide the webhook\'s id:', $this->webhookId)
            ->expectsOutput('Webhook was not deleted: Invalid');

        Http::assertSentInOrder([
            $this->assertDeleteWebhookRequest(),
        ]);
    }

    protected function assertDeleteWebhookRequest()
    {
        return function (Request $request) {
            $this->assertSame(
                config('brevo-webhook-manager.brevo.base_url')."webhooks/{$this->webhookId}",
                $request->url()
            );

            $this->assertSame('DELETE', $request->method());

            $this->assertSame([], $request->data());

            return true;
        };
    }
}
