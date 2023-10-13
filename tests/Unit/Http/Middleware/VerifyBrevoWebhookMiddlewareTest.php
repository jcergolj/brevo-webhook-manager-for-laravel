<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Middleware\VerifyBrevoWebhookMiddleware;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyBrevoWebhookMiddlewareTest extends TestCase
{
    /** @test */
    public function webhook_signature_is_outside_valid_range_abort()
    {
        $this->withExceptionHandling();
        $this->expectException(HttpException::class);

        $middleware = new VerifyBrevoWebhookMiddleware();

        $request = new Request(
            server: ['REMOTE_ADDR' => '129.0.0.1'],
        );

        $middleware->handle($request, function ($request) {
            $this->fail('Next middleware was called when it should not have been.');
        });
    }

    /** @test */
    public function signature_is_verified()
    {
        $request = new Request(
            server: ['REMOTE_ADDR' => '1.179.112.3'],
        );

        $expectedResponse = new Response('allowed', Response::HTTP_OK);
        $next = function () use ($expectedResponse) {
            return $expectedResponse;
        };

        $actualResponse = (new VerifyBrevoWebhookMiddleware())->handle($request, $next);

        $this->assertSame($expectedResponse, $actualResponse);
    }
}
