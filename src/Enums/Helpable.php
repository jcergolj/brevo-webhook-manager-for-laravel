<?php

namespace Jcergolj\BrevoWebhookManager\Enums;

trait Helpable
{
    public static function arrayOfValues(): array
    {
        return collect(self::cases())->map(function ($event) {
            return $event->value;
        })->toArray();
    }

    public static function fromStringArray($events)
    {
        return collect($events)->map(function ($event) {
            return self::from($event);
        })->toArray();
    }
}
