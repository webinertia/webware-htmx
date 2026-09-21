<?php

declare(strict_types=1);

/**
 * This file is part of the Webware Htmx package.
 *
 * Copyright (c) 2026 Joey Smith <jsmith@webinertia.net>
 * and contributors.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webware\Htmx;

use Laminas\Diactoros\ServerRequestFilter\FilterServerRequestInterface;
use Mezzio\Template\TemplateRendererInterface;

/**
 * @type DependenciesConfig array{
 *     aliases: array<string, class-string>,
 *     factories: array<class-string, class-string>,
 * }
 * @type TemplatesConfig array{
 *     map: array<string, string>,
 *     default_body: string,
 * }
 * @type ProviderConfig array{
 *     dependencies: DependenciesConfig,
 *     templates: TemplatesConfig,
 * }
 * @internal
 */
final readonly class ConfigProvider
{
    /** @return DependenciesConfig */
    private function getDependencies(): array
    {
        return [
            'aliases'   => [
                FilterServerRequestInterface::class => Request\ServerRequestFilter::class,
                TemplateRendererInterface::class    => View\LaminasRenderer::class,
            ],
            'factories' => [
                Http\Middleware\DetectAjaxRequestMiddleware::class => Http\Middleware\Container\DetectAjaxRequestMiddlewareFactory::class,
                Http\Middleware\DisableBodyMiddleware::class       => Http\Middleware\Container\DisableBodyMiddlewareFactory::class,
                View\LaminasRenderer::class                        => View\LaminasRendererFactory::class,
            ],
        ];
    }

    /** @return TemplatesConfig */
    private function getTemplates(): array
    {
        return [
            'map'          => [
                'body::default' => __DIR__ . '/../templates/body/default.phtml',
            ],
            'default_body' => 'body::default',
        ];
    }

    /** @return ProviderConfig */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }
}
