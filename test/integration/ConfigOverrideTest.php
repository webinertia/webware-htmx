<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Htmx;

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\HelperPluginManagerInterface;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Renderer\RendererInterface;
use Laminas\View\Resolver\TemplateMapResolver;
use Mezzio\LaminasView\LayoutHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\ConfigProvider;
use Webware\Htmx\View\LaminasRenderer;
use Webware\Htmx\View\LaminasRendererFactory;

#[CoversClass(LaminasRendererFactory::class)]
#[CoversClass(LaminasRenderer::class)]
#[CoversClass(ConfigProvider::class)]
final class ConfigOverrideTest extends TestCase
{
    private const string TEMPLATES = __DIR__ . '/templates';

    #[Test]
    public function theBodyAndTheLayoutAreEachNamedByOneKey(): void
    {
        // Only a body is named, so the content renders through it and nothing wraps it: no other key
        // naming a layout, in templates or in view_manager, is consulted.
        $merged = new ConfigAggregator([
            ConfigProvider::class,
            static fn(): array => [
                'templates' => [
                    'map'  => [
                        'body::default' => self::TEMPLATES . '/userland/body.phtml',
                    ],
                    'body' => 'body::default',
                ],
            ],
        ])->getMergedConfig();

        self::assertSame(
            '<section class="userland-body">Hello World</section>',
            $this->renderer($merged)->render('page::home', ['name' => 'World']),
        );
    }

    #[Test]
    public function userlandConfigOverridesTheDefaultBodyTemplate(): void
    {
        $userBody   = self::TEMPLATES . '/userland/body.phtml';
        $layoutFile = self::TEMPLATES . '/layout/default.phtml';

        // Standard Mezzio configuration: userland ConfigProvider/config file
        // registered AFTER the package ConfigProvider replaces the map entry.
        // The application also supplies a layout (as Mezzio always does): the
        // action template renders into the body, which renders into the layout.
        $merged = new ConfigAggregator([
            ConfigProvider::class,
            static fn(): array => [
                'templates' => [
                    'map'    => [
                        'body::default'   => $userBody,
                        'layout::default' => $layoutFile,
                    ],
                    'body'   => 'body::default',
                    'layout' => 'layout::default',
                ],
            ],
        ])->getMergedConfig();

        self::assertSame(
            '<html><section class="userland-body">Hello World</section></html>',
            $this->renderer($merged)->render('page::home', ['name' => 'World']),
        );
    }

    /**
     * @param array<string, mixed> $merged
     */
    private function renderer(array $merged): LaminasRenderer
    {
        $map =
            ['page::home' => self::TEMPLATES . '/page/home.phtml']
            + $merged['templates']['map'];

        $helpers = new HelperPluginManager(new ServiceManager(), [
            'factories' => [LayoutHelper::class => InvokableFactory::class],
        ]);
        $php = new PhpRenderer($helpers, new TemplateMapResolver($map), false);

        $container = new ServiceManager([
            'services' => [
                'config'                            => $merged,
                RendererInterface::class            => $php,
                HelperPluginManagerInterface::class => $helpers,
            ],
        ]);

        return (new LaminasRendererFactory())($container);
    }
}
