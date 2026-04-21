<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Middleware\VerifyBrevoWebhookBasicAuthMiddleware;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyBrevoWebhookBasicAuthMiddlewareTest extends TestCase
{
    #[Test]
    public function passes_with_matching_credentials()
    {
        config()->set('brevo-webhook-manager.auth.username', 'user');
        config()->set('brevo-webhook-manager.auth.password', 'secret');

        $request = new Request(server: [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW' => 'secret',
        ]);

        $response = (new VerifyBrevoWebhookBasicAuthMiddleware)
            ->handle($request, fn () => new Response('ok'));

        $this->assertSame('ok', $response->getContent());
    }

    #[Test]
    public function aborts_401_on_wrong_username()
    {
        config()->set('brevo-webhook-manager.auth.username', 'user');
        config()->set('brevo-webhook-manager.auth.password', 'secret');

        $request = new Request(server: [
            'PHP_AUTH_USER' => 'wrong',
            'PHP_AUTH_PW' => 'secret',
        ]);

        try {
            (new VerifyBrevoWebhookBasicAuthMiddleware)
                ->handle($request, fn () => $this->fail('should not pass'));
            $this->fail('Expected HttpException.');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_UNAUTHORIZED, $e->getStatusCode());
        }
    }

    #[Test]
    public function aborts_401_on_wrong_password()
    {
        config()->set('brevo-webhook-manager.auth.username', 'user');
        config()->set('brevo-webhook-manager.auth.password', 'secret');

        $request = new Request(server: [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW' => 'nope',
        ]);

        try {
            (new VerifyBrevoWebhookBasicAuthMiddleware)
                ->handle($request, fn () => $this->fail('should not pass'));
            $this->fail('Expected HttpException.');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_UNAUTHORIZED, $e->getStatusCode());
        }
    }

    #[Test]
    public function aborts_401_without_credentials()
    {
        config()->set('brevo-webhook-manager.auth.username', 'user');
        config()->set('brevo-webhook-manager.auth.password', 'secret');

        $request = new Request;

        try {
            (new VerifyBrevoWebhookBasicAuthMiddleware)
                ->handle($request, fn () => $this->fail('should not pass'));
            $this->fail('Expected HttpException.');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_UNAUTHORIZED, $e->getStatusCode());
        }
    }

    #[Test]
    public function aborts_500_when_unconfigured()
    {
        config()->set('brevo-webhook-manager.auth.username', null);
        config()->set('brevo-webhook-manager.auth.password', null);

        $request = new Request;

        try {
            (new VerifyBrevoWebhookBasicAuthMiddleware)
                ->handle($request, fn () => $this->fail('should not pass'));
            $this->fail('Expected HttpException.');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }
}
