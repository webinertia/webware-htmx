<?php

declare(strict_types=1);

namespace WebwareTest\Htmx;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\Request\Header;

#[CoversClass(Header::class)]
final class RequestHeaderTest extends TestCase
{
    /** @return array<string, array{Header, string}> */
    public static function headerProvider(): array
    {
        return [
            'Boosted'               => [Header::Boosted, 'hx-boosted'],
            'CurrentUrl'            => [Header::CurrentUrl, 'hx-current-url'],
            'HistoryRestoreRequest' => [Header::HistoryRestoreRequest, 'hx-history-restore-request'],
            'Prompt'                => [Header::Prompt, 'hx-prompt'],
            'Request'               => [Header::Request, 'hx-request'],
            'Target'                => [Header::Target, 'hx-target'],
            'TriggerName'           => [Header::TriggerName, 'hx-trigger-name'],
            'Trigger'               => [Header::Trigger, 'hx-trigger'],
        ];
    }

    #[Test]
    #[DataProvider('headerProvider')]
    public function itExposesTheExpectedHeaderValue(Header $case, string $value): void
    {
        self::assertSame($value, $case->value);
    }
}
