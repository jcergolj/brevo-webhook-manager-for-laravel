<?php

namespace Jcergolj\BrevoWebhookManager\Enums;

use Jcergolj\BrevoWebhookManager\Enums\Helpable;

enum WebhookTypes: string
{
    use Helpable;

    case TRANSACTIONAL = 'transactional';
    case MARKETING = 'marketing';
    case INBOUND = 'inbound';
}
