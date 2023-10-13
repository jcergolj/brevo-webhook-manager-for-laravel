<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Responses;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\CreateWebhookResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\HasStatus;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class CreateWebhookResponseTest extends TestCase
{
    /** @test */
    public function from_response()
    {
        $psr7Response = new Psr7Response(
            status: Response::HTTP_CREATED,
            body: json_encode(['id' => 123])
        );

        $clientResponse = new ClientResponse($psr7Response);

        $response = CreateWebhookResponse::fromResponse($clientResponse);

        $this->assertSame(123, $response->id);

        $this->assertSame(Response::HTTP_CREATED, $response->status);

        $this->assertSame($clientResponse, $response->original);
    }

    /** @test */
    public function asset_class_has_has_status_trait()
    {
        $this->assertContains(HasStatus::class, class_uses(CreateWebhookResponse::class));
    }
}
