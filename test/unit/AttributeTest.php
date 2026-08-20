<?php

declare(strict_types=1);

namespace WebwareTest\Htmx;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\Attribute;

#[CoversClass(Attribute::class)]
final class AttributeTest extends TestCase
{
    /** @return array<string, array{Attribute, string}> */
    public static function attributeProvider(): array
    {
        return [
            'Get'         => [Attribute::Get, 'hx-get'],
            'Post'        => [Attribute::Post, 'hx-post'],
            'On'          => [Attribute::On, 'hx-on'],
            'Push_Url'    => [Attribute::Push_Url, 'hx-push-url'],
            'Swap'        => [Attribute::Swap, 'hx-swap'],
            'Target'      => [Attribute::Target, 'hx-target'],
            'Trigger'     => [Attribute::Trigger, 'hx-trigger'],
            'Boost'       => [Attribute::Boost, 'hx-boost'],
            'Confirm'     => [Attribute::Confirm, 'hx-confirm'],
            'Delete'      => [Attribute::Delete, 'hx-delete'],
            'Replace_Url' => [Attribute::Replace_Url, 'hx-replace-url'],
            'Request'     => [Attribute::Request, 'hx-request'],
            'Validate'    => [Attribute::Validate, 'hx-validate'],
        ];
    }

    #[Test]
    #[DataProvider('attributeProvider')]
    public function itExposesTheExpectedAttributeName(Attribute $case, string $value): void
    {
        self::assertSame($value, $case->value);
    }
}
