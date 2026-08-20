<?php

declare(strict_types=1);

namespace WebwareTest\Htmx;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;
use Webware\Htmx\EnumTrait;
use Webware\Htmx\Response\Header;

#[CoversClass(Header::class)]
#[CoversTrait(EnumTrait::class)]
final class EnumTraitTest extends TestCase
{
    #[Test]
    public function fromNameReturnsMatchingCase(): void
    {
        self::assertSame(Header::Redirect, Header::fromName('Redirect'));
    }

    #[Test]
    public function fromNameThrowsValueErrorForUnknownName(): void
    {
        $this->expectException(ValueError::class);

        Header::fromName('NotACase');
    }

    #[Test]
    public function namesReturnsAllCaseNames(): void
    {
        $names = Header::names();

        self::assertContains('Location', $names);
        self::assertContains('TriggerAfterSwap', $names);
        self::assertCount(11, $names);
    }

    #[Test]
    public function toArrayNormalizedAppliesValueTreatment(): void
    {
        $map = Header::toArray(
            normalize     : true,
            valueTreatment: 'strtolower',
        );

        self::assertSame('hx-location', $map['Location']);
    }

    #[Test]
    public function toArrayNormalizedReturnsNameToValueMap(): void
    {
        $map = Header::toArray(normalize: true);

        self::assertSame('HX-Location', $map['Location']);
    }

    #[Test]
    public function toArrayReturnsValueToNameMapByDefault(): void
    {
        $map = Header::toArray();

        self::assertSame('Location', $map['HX-Location']);
    }

    #[Test]
    public function tryFromNameReturnsMatchingCase(): void
    {
        self::assertSame(Header::Location, Header::tryFromName('Location'));
    }

    #[Test]
    public function tryFromNameReturnsNullForUnknownName(): void
    {
        self::assertNull(Header::tryFromName('NotACase'));
    }

    #[Test]
    public function valuesReturnsAllCaseValues(): void
    {
        $values = Header::values();

        self::assertContains('HX-Location', $values);
        self::assertContains('HX-Trigger-After-Swap', $values);
        self::assertCount(11, $values);
    }
}
