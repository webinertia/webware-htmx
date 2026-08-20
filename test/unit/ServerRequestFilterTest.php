<?php

declare(strict_types=1);

namespace WebwareTest\Htmx;

use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\EnumTrait;
use Webware\Htmx\Request\Header;
use Webware\Htmx\Request\ServerRequestFilter;

#[CoversClass(ServerRequestFilter::class)]
#[UsesClass(Header::class)]
#[UsesTrait(EnumTrait::class)]
final class ServerRequestFilterTest extends TestCase
{
    private ServerRequestFilter $filter;

    #[Test]
    public function itConvertsLiteralTrueToBoolean(): void
    {
        $request = new ServerRequest([], [], 'http://example.com', 'GET', 'php://input', [
            'hx-boosted' => 'true',
        ]);

        $filtered = ($this->filter)($request);

        self::assertTrue($filtered->getAttribute('hx-boosted'));
    }

    #[Test]
    public function itIgnoresNonHtmxHeaders(): void
    {
        $request = new ServerRequest([], [], 'http://example.com', 'GET', 'php://input', [
            'Accept' => 'text/html',
        ]);

        $filtered = ($this->filter)($request);

        self::assertNull($filtered->getAttribute('accept'));
    }

    #[Test]
    public function itKeepsNonBooleanValuesAsStrings(): void
    {
        $request = new ServerRequest([], [], 'http://example.com', 'GET', 'php://input', [
            'HX-Trigger' => 'click',
        ]);

        $filtered = ($this->filter)($request);

        self::assertSame('click', $filtered->getAttribute('hx-trigger'));
    }

    #[Test]
    public function itSetsRequestAttributesForHtmxHeaders(): void
    {
        $request = new ServerRequest([], [], 'http://example.com', 'GET', 'php://input', [
            'HX-Request' => 'true',
            'HX-Target'  => 'main',
        ]);

        $filtered = ($this->filter)($request);

        self::assertTrue($filtered->getAttribute('hx-request'));
        self::assertSame('main', $filtered->getAttribute('hx-target'));
    }

    protected function setUp(): void
    {
        $this->filter = new ServerRequestFilter();
    }
}
