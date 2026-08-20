<?php

declare(strict_types=1);

namespace WebwareTest\Htmx;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\Response\Header;

#[CoversClass(Header::class)]
final class ResponseHeaderTest extends TestCase
{
    /** @return array<string, array{Header, string}> */
    public static function headerProvider(): array
    {
        return [
            'Location'           => [Header::Location, 'HX-Location'],
            'PushUrl'            => [Header::PushUrl, 'HX-Push-Url'],
            'Redirect'           => [Header::Redirect, 'HX-Redirect'],
            'Refresh'            => [Header::Refresh, 'HX-Refresh'],
            'ReplaceUrl'         => [Header::ReplaceUrl, 'HX-Replace-Url'],
            'Reswap'             => [Header::Reswap, 'HX-Reswap'],
            'Retarget'           => [Header::Retarget, 'HX-Retarget'],
            'Reselect'           => [Header::Reselect, 'HX-Reselect'],
            'Trigger'            => [Header::Trigger, 'HX-Trigger'],
            'TriggerAfterSettle' => [Header::TriggerAfterSettle, 'HX-Trigger-After-Settle'],
            'TriggerAfterSwap'   => [Header::TriggerAfterSwap, 'HX-Trigger-After-Swap'],
        ];
    }

    #[Test]
    #[DataProvider('headerProvider')]
    public function itExposesTheExpectedHeaderValue(Header $case, string $value): void
    {
        self::assertSame($value, $case->value);
    }
}
