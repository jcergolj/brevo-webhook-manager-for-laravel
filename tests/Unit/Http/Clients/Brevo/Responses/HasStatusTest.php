<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\HasStatus;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class HasStatusTest extends TestCase
{
    #[Test]
    public function success()
    {
        $response = new CustomResponse;
        $this->assertTrue($response->success());
    }

    #[Test]
    public function bad()
    {
        $response = new CustomResponse;
        $this->assertFalse($response->bad());
    }
}

class CustomResponse
{
    use HasStatus;
}
