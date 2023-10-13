<?php

namespace Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses;

trait HasStatus
{
    public function success(): bool
    {
        return true;
    }

    public function bad(): bool
    {
        return ! $this->success();
    }
}
