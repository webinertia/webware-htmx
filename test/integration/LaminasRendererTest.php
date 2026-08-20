<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Htmx;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver\TemplateMapResolver;
use Mezzio\LaminasView\LayoutHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Htmx\View\LaminasRenderer;

#[CoversClass(LaminasRenderer::class)]
final class LaminasRendererTest extends TestCase
{
    private const string TEMPLATES = __DIR__ . '/templates';

    #[Test]
    public function renderSkipsTheBodyLayerWhenDisabled(): void
    {
        $renderer = $this->createRenderer(body: 'body::default');

        self::assertSame(
            'Hello World',
            $renderer->render('page::home', ['name' => 'World', 'body' => false]),
        );
    }

    #[Test]
    public function renderWrapsPageContentInTheConfiguredBodyLayer(): void
    {
        $renderer = $this->createRenderer(body: 'body::default');

        self::assertSame(
            '<body>Hello World</body>',
            $renderer->render('page::home', ['name' => 'World']),
        );
    }

    #[Test]
    public function renderWrapsTheBodyInTheConfiguredLayout(): void
    {
        $renderer = $this->createRenderer(
            body  : 'body::default',
            layout: 'layout::default',
        );

        self::assertSame(
            '<html><body>Hello World</body></html>',
            $renderer->render('page::home', ['name' => 'World']),
        );
    }

    private function createRenderer(?string $body, ?string $layout = null): LaminasRenderer
    {
        $map = [
            'page::home'      => self::TEMPLATES . '/page/home.phtml',
            'body::default'   => self::TEMPLATES . '/body/default.phtml',
            'layout::default' => self::TEMPLATES . '/layout/default.phtml',
        ];

        $helpers = new HelperPluginManager(new ServiceManager(), [
            'factories' => [LayoutHelper::class => InvokableFactory::class],
        ]);
        $php = new PhpRenderer($helpers, new TemplateMapResolver($map), false);

        return new LaminasRenderer($php, $helpers, $layout, $body);
    }
}
