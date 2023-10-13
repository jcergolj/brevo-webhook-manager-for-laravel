<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\HasStatus;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class HasStatusTest extends TestCase
{
    /** @test */
    public function success()
    {
        $response = new CustomResponse;
        $this->assertTrue($response->success());
    }

    /** @test */
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
