<?php

namespace Jcergolj\BrevoWebhookManager\Tests;

use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\BrevoWebhookMangerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('brevo-webhook-manager.brevo.base_url', 'https://api.brevo.com/v3/');
        config()->set('brevo-webhook-manager.brevo.api_key', 'api-key');
        config()->set('brevo-webhook-manager.api_user_agent', 'user-agent');

        Http::preventStrayRequests();
    }

    protected function getPackageProviders($app)
    {
        return [
            BrevoWebhookMangerServiceProvider::class,
        ];
    }
}
