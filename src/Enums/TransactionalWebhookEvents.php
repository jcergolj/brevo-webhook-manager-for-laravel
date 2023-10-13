<?php

namespace Jcergolj\BrevoWebhookManager\Enums;

enum TransactionalWebhookEvents: string
{
    use Helpable;

    case SENT = 'sent';
    case REQUEST = 'request';
    case DELIVERED = 'delivered';
    case HARD_BOUNCE = 'hardBounce';
    case SOFT_BOUNCE = 'softBounce';
    case BLOCKED = 'blocked';
    case SPAM = 'spam';
    case INVALID = 'invalid';
    case DEFERRED = 'deferred';
    case CLICK = 'click';
    case OPENED = 'opened';
    case UNIQUE_OPENED = 'uniqueOpened';
    case UNSUBSCRIBED = 'unsubscribed';
}
