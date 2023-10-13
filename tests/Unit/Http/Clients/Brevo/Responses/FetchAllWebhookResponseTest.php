<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Unit\Http\Clients\Responses;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Jcergolj\BrevoWebhookManager\Collections\Webhooks;
use Jcergolj\BrevoWebhookManager\Enums\MarketingWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\TransactionalWebhookEvents;
use Jcergolj\BrevoWebhookManager\Enums\WebhookTypes;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\FetchAllWebhookResponse;
use Jcergolj\BrevoWebhookManager\Http\Clients\Brevo\Responses\HasStatus;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class FetchAllWebhookResponseTest extends TestCase
{
    /** @var array */
    public $webhook1;

    /** @var array */
    public $webhook2;

    public function setUp(): void
    {
        parent::setUp();

        $this->webhook1 = [
            'id' => 123,
            'url' => 'https://example.com',
            'description' => 'webhook description',
            'events' => [TransactionalWebhookEvents::BLOCKED->value, TransactionalWebhookEvents::CLICK->value],
            'type' => WebhookTypes::TRANSACTIONAL->value,
            'createdAt' => '2022-01-01T00:00:00Z',
            'modifiedAt' => '2022-01-01T00:00:00Z',
        ];

        $this->webhook2 = [
            'id' => 456,
            'url' => 'https://test.com',
            'description' => 'test description',
            'events' => [MarketingWebhookEvents::CLICK->value],
            'type' => WebhookTypes::MARKETING->value,
            'createdAt' => '2023-01-01T00:00:00Z',
            'modifiedAt' => '2023-01-01T00:00:00Z',
        ];
    }

    /** @test */
    public function from_response()
    {
        $psr7Response = new Psr7Response(
            status: Response::HTTP_OK,
            body: json_encode(['webhooks' => [$this->webhook1, $this->webhook2]])
        );

        $clientResponse = new ClientResponse($psr7Response);

        $response = FetchAllWebhookResponse::fromResponse($clientResponse);

        $this->assertSame($clientResponse, $response->original);

        $this->assertInstanceOf(FetchAllWebhookResponse::class, $response);

        $this->assertInstanceOf(ClientResponse::class, $response->original);

        $this->assertEquals(Response::HTTP_OK, $response->status);

        $this->assertInstanceOf(Webhooks::class, $response->webhooks);
    }

    /** @test */
    public function asset_class_has_has_status_trait()
    {
        $this->assertContains(HasStatus::class, class_uses(FetchAllWebhookResponse::class));
    }
}
