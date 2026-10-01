<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Htmx;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFilter\FilterServerRequestInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\ConfigProvider;
use Webware\Htmx\Request\Header;
use Webware\Htmx\Request\ServerRequestFilter;

/**
 * Mezzio builds the request with `$container->has(FilterServerRequestInterface::class) ? get : null`,
 * and a service manager answers `has()` false for an alias whose target is not registered. The filter
 * is what sets the `hx-request` attribute DetectAjaxRequestMiddleware reads, so it has to be reachable
 * through that alias.
 */
#[CoversClass(ConfigProvider::class)]
#[CoversMethod(ConfigProvider::class, 'getDependencies')]
final class ServerRequestFilterWiringTest extends TestCase
{
    #[Test]
    public function theContainerHasTheFilterServiceMezzioAsksFor(): void
    {
        static::assertTrue($this->container()->has(FilterServerRequestInterface::class));
    }

    #[Test]
    public function theFilterServiceIsTheHtmxFilterAndSetsTheRequestAttribute(): void
    {
        $filter = $this->container()->get(FilterServerRequestInterface::class);

        static::assertInstanceOf(ServerRequestFilter::class, $filter);

        $request = $filter(new ServerRequest([], [], '/', 'GET', 'php://input', ['hx-request' => 'true']));

        static::assertTrue($request->getAttribute(Header::Request->value, false));
    }

    private function container(): ServiceManager
    {
        $dependencies = new ConfigProvider()->getDependencies();

        return new ServiceManager([
            'aliases'    => $dependencies['aliases'],
            'invokables' => $dependencies['invokables'],
        ]);
    }
}
