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

namespace Webware\Htmx\View;

use Laminas\View\HelperPluginManagerInterface;
use Laminas\View\Renderer\RendererInterface;
use Mezzio\Template\Exception\ExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_iterable;
use function is_string;
use function iterator_to_array;

/**
 * Create and return a LaminasView template instance.
 *
 * This factory works on the basis that laminas-view is correctly configured, and we can retrieve
 * Laminas\View\View from the container along with our own namespaced path stack resolver.
 *
 * A configuration array is expected with the key `config`, the structure of which is
 * documented in {@link ConfigProvider}.
 *
 * @internal
 *
 * @psalm-internal Mezzio\LaminasView
 * @psalm-internal MezzioTest\LaminasView
 */
final class LaminasRendererFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ExceptionInterface
     */
    public function __invoke(ContainerInterface $container): LaminasRenderer
    {
        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_iterable($config) ? iterator_to_array($config) : [];

        /**
         * The body and the layout are each named by one configuration value, and each value is a
         * template address — `body::default`, `layout::default` — so whatever resolver is registered
         * decides which file that address points at. Neither key is required: with no body the
         * content renders alone, and with no layout nothing wraps it.
         */
        $layout = $config['templates']['layout'] ?? null;
        $body   = $config['templates']['body'] ?? null;

        return new LaminasRenderer(
            $container->get(RendererInterface::class),
            $container->get(HelperPluginManagerInterface::class),
            is_string($layout) && '' !== $layout ? $layout : null,
            is_string($body) && '' !== $body ? $body : null,
        );
    }
}
