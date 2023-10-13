<?php

namespace Jcergolj\BrevoWebhookManager\Enums;

enum InboundWebhookEvents: string
{
    use Helpable;

    case INBOUND_EMAIL_PROCESSED = 'inboundEmailProcessed';
}
