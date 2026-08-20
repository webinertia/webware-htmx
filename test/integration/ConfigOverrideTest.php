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
                    'map' => [
                        'body::default'   => $userBody,
                        'layout::default' => $layoutFile,
                    ],
                    'default_layout' => 'layout::default',
                ],
            ],
        ])->getMergedConfig();

        $map = ['page::home' => self::TEMPLATES . '/page/home.phtml']
            + $merged['templates']['map'];

        $helpers = new HelperPluginManager(new ServiceManager(), [
            'factories' => [LayoutHelper::class => InvokableFactory::class],
        ]);
        $php = new PhpRenderer($helpers, new TemplateMapResolver($map), false);

        $container = new ServiceManager([
            'services' => [
                'config'                        => $merged,
                RendererInterface::class        => $php,
                HelperPluginManagerInterface::class => $helpers,
            ],
        ]);

        $renderer = (new LaminasRendererFactory())($container);

        self::assertSame(
            '<html><section class="userland-body">Hello World</section></html>',
            $renderer->render('page::home', ['name' => 'World']),
        );
    }
}
