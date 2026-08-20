<?php

declare(strict_types=1);

namespace WebwareTest\Htmx;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\Swap;

#[CoversClass(Swap::class)]
final class SwapTest extends TestCase
{
    /** @return array<string, array{Swap, string}> */
    public static function swapProvider(): array
    {
        return [
            'InnerHTML'   => [Swap::InnerHTML, 'innerHTML'],
            'OuterHTML'   => [Swap::OuterHTML, 'outerHTML'],
            'BeforeBegin' => [Swap::BeforeBegin, 'beforebegin'],
            'AfterBegin'  => [Swap::AfterBegin, 'afterbegin'],
            'BeforeEnd'   => [Swap::BeforeEnd, 'beforeend'],
            'AfterEnd'    => [Swap::AfterEnd, 'afterend'],
            'Delete'      => [Swap::Delete, 'delete'],
            'None'        => [Swap::None, 'none'],
        ];
    }

    #[Test]
    #[DataProvider('swapProvider')]
    public function itExposesTheExpectedSwapValue(Swap $case, string $value): void
    {
        self::assertSame($value, $case->value);
    }
}
