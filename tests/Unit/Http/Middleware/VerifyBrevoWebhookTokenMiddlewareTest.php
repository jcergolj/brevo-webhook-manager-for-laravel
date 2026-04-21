<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Middleware\VerifyBrevoWebhookTokenMiddleware;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyBrevoWebhookTokenMiddlewareTest extends TestCase
{
    #[Test]
    public function passes_with_valid_bearer_token()
    {
        config()->set('brevo-webhook-manager.auth.token', 'shh');
        config()->set('brevo-webhook-manager.auth.token_header', 'Authorization');

        $request = Request::create('/hook', 'POST');
        $request->headers->set('Authorization', 'Bearer shh');

        $response = (new VerifyBrevoWebhookTokenMiddleware)
            ->handle($request, fn () => new Response('ok'));

        $this->assertSame('ok', $response->getContent());
    }

    #[Test]
    public function aborts_401_on_wrong_bearer_token()
    {
        config()->set('brevo-webhook-manager.auth.token', 'shh');

        $request = Request::create('/hook', 'POST');
        $request->headers->set('Authorization', 'Bearer nope');

        try {
            (new VerifyBrevoWebhookTokenMiddleware)
                ->handle($request, fn () => $this->fail('should not pass'));
            $this->fail('Expected HttpException.');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_UNAUTHORIZED, $e->getStatusCode());
        }
    }

    #[Test]
    public function aborts_401_when_header_missing()
    {
        config()->set('brevo-webhook-manager.auth.token', 'shh');

        $request = Request::create('/hook', 'POST');

        try {
            (new VerifyBrevoWebhookTokenMiddleware)
                ->handle($request, fn () => $this->fail('should not pass'));
            $this->fail('Expected HttpException.');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_UNAUTHORIZED, $e->getStatusCode());
        }
    }

    #[Test]
    public function passes_with_custom_header_raw_value()
    {
        config()->set('brevo-webhook-manager.auth.token', 'shh');
        config()->set('brevo-webhook-manager.auth.token_header', 'X-Brevo-Token');

        $request = Request::create('/hook', 'POST');
        $request->headers->set('X-Brevo-Token', 'shh');

        $response = (new VerifyBrevoWebhookTokenMiddleware)
            ->handle($request, fn () => new Response('ok'));

        $this->assertSame('ok', $response->getContent());
    }

    #[Test]
    public function aborts_401_when_custom_header_wrong()
    {
        config()->set('brevo-webhook-manager.auth.token', 'shh');
        config()->set('brevo-webhook-manager.auth.token_header', 'X-Brevo-Token');

        $request = Request::create('/hook', 'POST');
        $request->headers->set('X-Brevo-Token', 'nope');

        try {
            (new VerifyBrevoWebhookTokenMiddleware)
                ->handle($request, fn () => $this->fail('should not pass'));
            $this->fail('Expected HttpException.');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_UNAUTHORIZED, $e->getStatusCode());
        }
    }

    #[Test]
    public function aborts_500_when_unconfigured()
    {
        config()->set('brevo-webhook-manager.auth.token', null);

        $request = Request::create('/hook', 'POST');

        try {
            (new VerifyBrevoWebhookTokenMiddleware)
                ->handle($request, fn () => $this->fail('should not pass'));
            $this->fail('Expected HttpException.');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }
}
