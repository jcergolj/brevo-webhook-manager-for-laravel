<?php

namespace Jcergolj\BrevoWebhookManager\Enums;

enum MarketingWebhookEvents: string
{
    use Helpable;

    case SPAM = 'spam';
    case OPENED = 'opened';
    case CLICK = 'click';
    case HARD_BOUNCE = 'hardBounce';
    case SOFT_BOUNCE = 'softBounce';
    case UNSUBSCRIBED = 'unsubscribed';
    case LIST_ADDITION = 'listAddition';
    case DELIVERED = 'delivered';
}
