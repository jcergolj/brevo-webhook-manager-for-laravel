<?php

namespace Jcergolj\BrevoWebhookManager;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Jcergolj\BrevoWebhookManager\Console\Commands\CreateInboundWebhook;
use Jcergolj\BrevoWebhookManager\Console\Commands\CreateMarketingWebhook;
use Jcergolj\BrevoWebhookManager\Console\Commands\CreateTransactionalWebhook;
use Jcergolj\BrevoWebhookManager\Console\Commands\DeleteWebhook;
use Jcergolj\BrevoWebhookManager\Console\Commands\FetchWebhook;
use Jcergolj\BrevoWebhookManager\Console\Commands\FetchWebhooks;
use Jcergolj\BrevoWebhookManager\Console\Commands\UpdateWebhook;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\BrevoMacro;

class BrevoWebhookMangerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/brevo-webhook-manager.php' => config_path('brevo-webhook-manager.php'),
        ], 'brevo-webhook-manager');

        Http::mixin(new BrevoMacro());

        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchWebhooks::class,
                CreateTransactionalWebhook::class,
                CreateMarketingWebhook::class,
                CreateInboundWebhook::class,
                UpdateWebhook::class,
                DeleteWebhook::class,
                FetchWebhook::class,
            ]);
        }
    }
}
