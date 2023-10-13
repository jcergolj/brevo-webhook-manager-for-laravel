<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Responses;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\FetchWebhookResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\HasStatus;
use Jcergolj\BrevoWebhookManager\Items\Webhook;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class FetchWebhookResponseTest extends TestCase
{
    /** @var array */
    public $attributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->attributes = [
            'id' => 123,
            'url' => 'https://example.com',
            'description' => 'webhook description',
            'events' => [TransactionalWebhookEvents::BLOCKED->value, TransactionalWebhookEvents::CLICK->value],
            'type' => WebhookTypes::TRANSACTIONAL->value,
            'createdAt' => '2016-06-07T09:10:10Z',
            'modifiedAt' => '2016-06-07T09:10:10Z',
        ];
    }

    /** @test */
    public function from_response()
    {
        $psr7Response = new Psr7Response(
            status: Response::HTTP_OK,
            body: json_encode($this->attributes)
        );

        $clientResponse = new ClientResponse($psr7Response);

        $response = FetchWebhookResponse::fromResponse($clientResponse);

        $this->assertSame($clientResponse, $response->original);

        $this->assertInstanceOf(FetchWebhookResponse::class, $response);

        $this->assertInstanceOf(ClientResponse::class, $response->original);

        $this->assertEquals(Response::HTTP_OK, $response->status);

        $this->assertInstanceOf(Webhook::class, $response->webhook);
    }

    /** @test */
    public function asset_class_has_has_status_trait()
    {
        $this->assertContains(HasStatus::class, class_uses(FetchWebhookResponse::class));
    }
}
