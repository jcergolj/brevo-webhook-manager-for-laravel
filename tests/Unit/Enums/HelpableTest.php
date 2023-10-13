<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Jcergolj\BrevoWebhookManager\Enums\Helpable;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;

class HelpableTest extends TestCase
{
    /** @test */
    public function array_of_values()
    {
        $this->assertSame(['click', 'open'], TestEnum::arrayOfValues());
    }

    /** @test */
    public function from_string_array()
    {
        $this->assertSame([TestEnum::CLICK, TestEnum::OPEN], TestEnum::fromStringArray(['click', 'open']));
    }
}

enum TestEnum: string
{
    use Helpable;

    case CLICK = 'click';
    case OPEN = 'open';
}
