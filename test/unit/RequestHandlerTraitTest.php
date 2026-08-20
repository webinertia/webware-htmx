<?php

declare(strict_types=1);

namespace WebwareTest\Htmx;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\RequestHandlerTrait;
use Webware\Htmx\Response\Header;

use function json_decode;

#[CoversTrait(RequestHandlerTrait::class)]
#[CoversClass(Header::class)]
final class RequestHandlerTraitTest extends TestCase
{
    private object $handler;

    #[Test]
    public function hxLocationAllowsParamsToOverrideTarget(): void
    {
        $headers = $this->handler->locationHeaders(['path' => '/users', 'target' => '#content']);

        self::assertSame(
            ['path' => '/users', 'target' => '#content'],
            json_decode($headers[Header::Location->value], associative: true),
        );
    }

    #[Test]
    public function hxLocationReturnsJsonWithDefaultDomTarget(): void
    {
        $headers = $this->handler->locationHeaders(['path' => '/users']);

        self::assertSame(
            ['path' => '/users', 'target' => 'main'],
            json_decode($headers[Header::Location->value], associative: true),
        );
    }

    protected function setUp(): void
    {
        $this->handler = new class() {
            use RequestHandlerTrait;

            /** @return array<string, string> */
            public function locationHeaders(array $params): array
            {
                return $this->hxLocation($params);
            }
        };
    }
}
