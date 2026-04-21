<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Middleware\VerifyBrevoWebhookMiddleware;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyBrevoWebhookMiddlewareTest extends TestCase
{
    #[Test]
    public function webhook_signature_is_outside_valid_range_abort()
    {
        $this->withExceptionHandling();
        $this->expectException(HttpException::class);

        $middleware = new VerifyBrevoWebhookMiddleware;

        $request = new Request(
            server: ['REMOTE_ADDR' => '129.0.0.1'],
        );

        $middleware->handle($request, function ($request) {
            $this->fail('Next middleware was called when it should not have been.');
        });
    }

    #[Test]
    public function signature_is_verified()
    {
        $request = new Request(
            server: ['REMOTE_ADDR' => '1.179.112.3'],
        );

        $expectedResponse = new Response('allowed', Response::HTTP_OK);
        $next = function () use ($expectedResponse) {
            return $expectedResponse;
        };

        $actualResponse = (new VerifyBrevoWebhookMiddleware)->handle($request, $next);

        $this->assertSame($expectedResponse, $actualResponse);
    }

    #[Test]
    public function valid_cidrs_can_be_overridden_via_config()
    {
        config()->set('brevo-webhook-manager.valid_cidrs', ['10.0.0.0/24']);

        $request = new Request(server: ['REMOTE_ADDR' => '10.0.0.5']);

        $response = (new VerifyBrevoWebhookMiddleware)->handle($request, fn () => new Response('ok'));

        $this->assertSame('ok', $response->getContent());
    }

    #[Test]
    public function cloudflare_ip_is_rejected_when_toggle_off()
    {
        $this->withExceptionHandling();
        $this->expectException(HttpException::class);

        config()->set('brevo-webhook-manager.allow_cloudflare', false);

        $request = new Request(server: ['REMOTE_ADDR' => '173.245.48.1']);

        (new VerifyBrevoWebhookMiddleware)->handle($request, fn () => $this->fail('should not pass'));
    }

    #[Test]
    public function cloudflare_ip_is_accepted_when_toggle_on()
    {
        config()->set('brevo-webhook-manager.allow_cloudflare', true);

        $request = new Request(server: ['REMOTE_ADDR' => '173.245.48.1']);

        $response = (new VerifyBrevoWebhookMiddleware)->handle($request, fn () => new Response('ok'));

        $this->assertSame('ok', $response->getContent());
    }
}
