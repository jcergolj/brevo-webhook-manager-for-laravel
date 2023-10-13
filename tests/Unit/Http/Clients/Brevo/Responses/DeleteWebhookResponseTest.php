<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Responses;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\DeleteWebhookResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\HasStatus;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class DeleteWebhookResponseTest extends TestCase
{
    /** @test */
    public function from_response()
    {
        $psr7Response = new Psr7Response(
            status: Response::HTTP_NO_CONTENT,
        );

        $clientResponse = new ClientResponse($psr7Response);

        $response = DeleteWebhookResponse::fromResponse($clientResponse);

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->status);
        $this->assertSame($clientResponse, $response->original);
    }

    /** @test */
    public function asset_class_has_has_status_trait()
    {
        $this->assertContains(HasStatus::class, class_uses(DeleteWebhookResponse::class));
    }
}
