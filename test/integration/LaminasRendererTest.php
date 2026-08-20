<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Htmx;

use FilesystemIterator;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver\TemplateMapResolver;
use Mezzio\LaminasView\LayoutHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Webware\Htmx\View\LaminasRenderer;

use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

#[CoversClass(LaminasRenderer::class)]
final class LaminasRendererTest extends TestCase
{
    private string $templatesDir;

    #[Test]
    public function renderSkipsTheBodyLayerWhenDisabled(): void
    {
        $renderer = $this->createRenderer(body: 'body::body');

        self::assertSame(
            'Hello World',
            $renderer->render('page::home', ['name' => 'World', 'body' => false]),
        );
    }

    #[Test]
    public function renderWrapsPageContentInTheConfiguredBodyLayer(): void
    {
        $renderer = $this->createRenderer(body: 'body::body');

        self::assertSame(
            '<body>Hello World</body>',
            $renderer->render('page::home', ['name' => 'World']),
        );
    }

    #[Test]
    public function renderWrapsTheBodyInTheConfiguredLayout(): void
    {
        $renderer = $this->createRenderer(
            body  : 'body::body',
            layout: 'layout::layout',
        );

        self::assertSame(
            '<html><body>Hello World</body></html>',
            $renderer->render('page::home', ['name' => 'World']),
        );
    }

    protected function setUp(): void
    {
        $this->templatesDir = sys_get_temp_dir() . '/htmx-test-' . uniqid();

        $this->writeTemplate('page/home.phtml', 'Hello <?= $this->name ?>');
        $this->writeTemplate('body/body.phtml', '<body><?= $this->content ?></body>');
        $this->writeTemplate('layout/layout.phtml', '<html><?= $this->body ?></html>');
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->templatesDir);
    }

    private function createRenderer(?string $body, ?string $layout = null): LaminasRenderer
    {
        $map = [
            'page::home'     => "{$this->templatesDir}/page/home.phtml",
            'body::body'     => "{$this->templatesDir}/body/body.phtml",
            'layout::layout' => "{$this->templatesDir}/layout/layout.phtml",
        ];

        $helpers = new HelperPluginManager(new ServiceManager(), [
            'factories' => [LayoutHelper::class => InvokableFactory::class],
        ]);
        $php = new PhpRenderer($helpers, new TemplateMapResolver($map), false);

        return new LaminasRenderer($php, $helpers, $layout, $body);
    }

    private function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
                continue;
            }

            unlink($item->getPathname());
        }

        rmdir($dir);
    }

    private function writeTemplate(string $relative, string $content): void
    {
        $path = "{$this->templatesDir}/{$relative}";
        mkdir(
            directory  : dirname($path),
            permissions: 0o777,
            recursive  : true,
        );
        file_put_contents($path, $content);
    }
}
