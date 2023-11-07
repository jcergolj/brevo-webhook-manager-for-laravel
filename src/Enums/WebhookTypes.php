<?php

namespace Jcergolj\BrevoWebhookManager\Enums;

enum WebhookTypes: string
{
    use Helpable;

    case TRANSACTIONAL = 'transactional';
    case MARKETING = 'marketing';
    case INBOUND = 'inbound';
}
