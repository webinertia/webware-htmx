<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Htmx;

use FilesystemIterator;
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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Webware\Htmx\ConfigProvider;
use Webware\Htmx\View\LaminasRenderer;
use Webware\Htmx\View\LaminasRendererFactory;

use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

#[CoversClass(LaminasRendererFactory::class)]
#[CoversClass(LaminasRenderer::class)]
#[CoversClass(ConfigProvider::class)]
final class ConfigOverrideTest extends TestCase
{
    private string $templatesDir;

    #[Test]
    public function userlandConfigOverridesTheDefaultBodyTemplate(): void
    {
        $userBody = "{$this->templatesDir}/user/body.phtml";

        // Standard Mezzio configuration: userland ConfigProvider/config file
        // registered AFTER the package ConfigProvider replaces the map entry.
        $merged = new ConfigAggregator([
            ConfigProvider::class,
            static fn(): array => [
                'templates' => ['map' => ['body::default' => $userBody]],
            ],
        ])->getMergedConfig();

        $map =
            ['page::home' => "{$this->templatesDir}/page/home.phtml"]
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

        $renderer = (new LaminasRendererFactory())($container);

        self::assertSame(
            '<section class="userland-body">Hello World</section>',
            $renderer->render('page::home', ['name' => 'World']),
        );
    }

    protected function setUp(): void
    {
        $this->templatesDir = sys_get_temp_dir() . '/htmx-test-' . uniqid();

        $this->writeTemplate('page/home.phtml', 'Hello <?= $this->name ?>');
        $this->writeTemplate('user/body.phtml', '<section class="userland-body"><?= $this->content ?></section>');
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->templatesDir);
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
