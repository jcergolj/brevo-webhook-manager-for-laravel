<?php

namespace Jcergolj\BrevoWebhookManager\Tests\Feature\Console\Commands;

use Jcergolj\BrevoWebhookManager\Enums\Helpable;
use Jcergolj\BrevoWebhookManager\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class HelpableTest extends TestCase
{
    #[Test]
    public function array_of_values()
    {
        $this->assertSame(['click', 'open'], TestEnum::arrayOfValues());
    }

    #[Test]
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
