<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Responses;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\BadResponse;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class BadResponseTest extends TestCase
{
    /** @test */
    public function from_response()
    {
        $psr7Response = new Psr7Response(
            status: Response::HTTP_BAD_REQUEST,
            body: json_encode(['message' => 'invalid', 'code' => 'bad request'])
        );

        $response = new ClientResponse($psr7Response);

        $badResponse = BadResponse::fromResponse($response);

        $this->assertSame('invalid', $badResponse->error);
        $this->assertSame('bad request', $badResponse->code);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $badResponse->status);
    }
}
