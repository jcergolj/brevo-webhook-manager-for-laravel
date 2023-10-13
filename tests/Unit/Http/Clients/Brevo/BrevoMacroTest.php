<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Brevo;

use Illuminate\Support\Facades\Http;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;
use ReflectionClass;

class BrevoMacroTest extends TestCase
{
    /** @var PendingRequest */
    public $pendingRequest;

    public function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        $this->pendingRequest = Http::presetting()->dump();
    }

    /** @test */
    public function assert_http_client_has_presetting_method()
    {
        $this->assertTrue(Http::hasMacro('presetting'));
    }

    /** @test */
    public function assert_api_key_header()
    {
        $this->assertSame(config('brevo-webhook-manager.brevo.api_key'), $this->pendingRequest->getOptions()['headers']['api-key']);
    }

    /** @test */
    public function assert_accept_header_is_set()
    {
        $this->assertSame('application/json', $this->pendingRequest->getOptions()['headers']['accept']);
    }

    /** @test */
    public function assert_content_type_header_is_set()
    {
        $this->assertSame('application/json', $this->pendingRequest->getOptions()['headers']['content-type']);
    }

    /** @test */
    public function assert_user_agent_header_is_set()
    {
        $this->assertSame(config('brevo-webhook-manager.api_user_agent'), $this->pendingRequest->getOptions()['headers']['User-Agent']);
    }

    /** @test */
    public function assert_base_url_is_set()
    {
        $pendingRequest = Http::presetting()->dump();

        $reflectionClass = new ReflectionClass($pendingRequest);
        $baseUrl = $reflectionClass->getProperty('baseUrl');

        $baseUrl->setAccessible(true);

        $this->assertSame(config('brevo-webhook-manager.brevo.base_url'), $baseUrl->getValue($pendingRequest));
    }
}
