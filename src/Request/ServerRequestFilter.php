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

namespace Webware\Htmx\Request;

use Laminas\Diactoros\Exception\ExceptionInterface;
use Laminas\Diactoros\ServerRequestFilter\FilterServerRequestInterface;
use Laminas\Diactoros\ServerRequestFilter\FilterUsingXForwardedHeaders;
use Override;
use Psr\Http\Message\ServerRequestInterface;

use function array_flip;
use function array_key_exists;
use function strtolower;

final class ServerRequestFilter implements FilterServerRequestInterface
{
    /**
     * @throws ExceptionInterface
     */
    #[Override]
    public function __invoke(ServerRequestInterface $request): ServerRequestInterface
    {
        // maintain default behavior
        $request = FilterUsingXForwardedHeaders::trustReservedSubnets()($request);

        /** @var array<string, string[]> $headers */
        $headers     = $request->getHeaders();
        $htmxHeaders = array_flip(Header::toArray(
            normalize     : true,
            valueTreatment: 'strtolower',
        ));

        foreach ($headers as $header => $value) {
            $normalized = strtolower($header);
            if (array_key_exists($normalized, $htmxHeaders)) {
                $request = $request->withAttribute(
                    $normalized,
                    'true' === $value[0] ? true : $value[0],
                );
            }
        }

        return $request;
    }
}
