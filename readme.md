# Brevo Webhook Manager CLI Tool for Laravel
This CLI tool provides a convenient way to manage Brevo webhooks from the command line. It provides a number of commands for fetching, creating, updating, and deleting webhooks.

<img src="imgs/create-webhook.png" alt="create brevo webhook example">

Features:
- Fetch webhooks from the Brevo API
- Create new webhooks
- Update existing webhooks
- Delete webhooks

## Installation

### Requirements
Required PHP >=8.2

### Installing package
```bash
composer require jcergolj/brevo-webhook-manager-for-laravel
```

### Publishing config file
```bash
php artisan vendor:publish --provider="Jcergolj\BrevoWebhookManager\BrevoWebhookMangerServiceProvider"
```

### .env file
```bash
BREVO_API_KEY=
USER_AGENT=""
BREVO_BASE_URL=https://api.brevo.com/v3/

# Optional — override the default Brevo IP ranges (comma-separated CIDRs)
BREVO_VALID_CIDRS=1.179.112.0/20,172.246.240.0/20

# Optional — allow requests coming through Cloudflare edge IPs
BREVO_ALLOW_CLOUDFLARE=false

# Required when using VerifyBrevoWebhookBasicAuthMiddleware
BREVO_WEBHOOK_USERNAME=
BREVO_WEBHOOK_PASSWORD=

# Required when using VerifyBrevoWebhookTokenMiddleware
BREVO_WEBHOOK_TOKEN=
# Optional — header name carrying the token (default: Authorization)
BREVO_WEBHOOK_TOKEN_HEADER=Authorization
```

## Securing incoming webhooks

Two middlewares are shipped. Register them on your webhook route.

### 1. IP allow-list — `VerifyBrevoWebhookMiddleware`
Rejects any request whose source IP is outside the configured CIDRs. Defaults to Brevo's published ranges. Set `BREVO_ALLOW_CLOUDFLARE=true` if your webhook route sits behind Cloudflare so edge IPs are accepted too. Override the base list via `BREVO_VALID_CIDRS`.

### 2. Basic auth — `VerifyBrevoWebhookBasicAuthMiddleware`
Implements [Brevo username/password authentication](https://developers.brevo.com/docs/username-and-password-authentication). When creating the webhook on Brevo, embed credentials in the URL:

```
https://USERNAME:PASSWORD@your-app.test/brevo/webhook
```

Set the same values in `.env` as `BREVO_WEBHOOK_USERNAME` and `BREVO_WEBHOOK_PASSWORD`. The middleware compares with `hash_equals` and aborts `401` on mismatch. If either env var is missing, the middleware aborts `500` (fail-closed).

### 3. Token auth — `VerifyBrevoWebhookTokenMiddleware`
Verifies a shared secret token sent in a request header. Defaults to reading `Authorization: Bearer <token>`. Configure a different header via `BREVO_WEBHOOK_TOKEN_HEADER` (e.g. `X-Brevo-Token`). When using a custom header, the raw header value is compared directly (no `Bearer` prefix). Set `BREVO_WEBHOOK_TOKEN` to the shared secret. Aborts `401` on mismatch, `500` if unconfigured.

### 4. See the Brevo docs
https://developers.brevo.com/docs/username-and-password-authentication

### Route example
```php
use Jcergolj\BrevoWebhookManager\Http\Middleware\VerifyBrevoWebhookBasicAuthMiddleware;
use Jcergolj\BrevoWebhookManager\Http\Middleware\VerifyBrevoWebhookMiddleware;
use Jcergolj\BrevoWebhookManager\Http\Middleware\VerifyBrevoWebhookTokenMiddleware;

Route::post('/brevo/webhook', BrevoWebhookController::class)
    ->middleware([
        VerifyBrevoWebhookMiddleware::class,
        VerifyBrevoWebhookBasicAuthMiddleware::class, // or VerifyBrevoWebhookTokenMiddleware::class
    ]);
```

### Avaliable commands
```bash
php artisan brevo-webhooks:create-inbound
```

```bash
php artisan brevo-webhooks:create-marketing
```

```bash
php artisan brevo-webhooks:create-transactional
```

```bash
php artisan brevo-webhooks:update
```

```bash
php artisan brevo-webhooks:delete
```

```bash
php artisan brevo-webhooks:fetch
```

```bash
php artisan brevo-webhooks:fetch-all
```
