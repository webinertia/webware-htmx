<?php

declare(strict_types=1);

namespace WebwareTest\Htmx;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\Response\Header;
use Webware\Htmx\Response\ResponseTrait;
use Webware\Htmx\TriggerTrait;

use function json_decode;

#[CoversTrait(ResponseTrait::class)]
#[CoversTrait(TriggerTrait::class)]
#[CoversClass(Header::class)]
final class ResponseTraitTest extends TestCase
{
    private object $response;

    #[Test]
    public function htmxLocationWithoutTargetSetsPlainPath(): void
    {
        $this->response->htmxLocation('/dashboard');

        self::assertSame('/dashboard', $this->response->getHeaders()[Header::Location->value]);
    }

    #[Test]
    public function htmxLocationWithTargetSetsJsonPayload(): void
    {
        $this->response->htmxLocation('/dashboard', '#main');

        self::assertSame(
            ['path' => '/dashboard', 'target' => '#main'],
            json_decode($this->response->getHeaders()[Header::Location->value], associative: true),
        );
    }

    #[Test]
    public function htmxTriggerHonorsEventAndHeaderArguments(): void
    {
        $this->response->htmxTrigger(['message' => 'ok'], 'toast', Header::Trigger);

        self::assertSame(
            ['toast' => ['message' => 'ok']],
            json_decode($this->response->getHeaders()[Header::Trigger->value], associative: true),
        );
    }

    #[Test]
    public function htmxTriggerUsesSystemMessageEventAndSettleHeaderByDefault(): void
    {
        $this->response->htmxTrigger(['message' => 'ok']);

        self::assertSame(
            ['systemMessage' => ['message' => 'ok']],
            json_decode($this->response->getHeaders()[Header::TriggerAfterSettle->value], associative: true),
        );
    }

    protected function setUp(): void
    {
        $this->response = new class() {
            use ResponseTrait;

            /** @return array<string, string> */
            public function getHeaders(): array
            {
                return $this->headers;
            }
        };
    }
}
