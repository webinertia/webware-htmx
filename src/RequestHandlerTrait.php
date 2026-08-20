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

use JsonException;
use Webware\Htmx\Response\Header;

use function json_encode;

use const JSON_THROW_ON_ERROR;

/**
 * @api
 */
trait RequestHandlerTrait
{
    private string $domTarget = 'main';

    /**
     * @param array<string, mixed> $params
     * @return array<string, string>
     * @throws JsonException
     */
    private function hxLocation(array $params): array
    {
        $data = ['target' => $this->domTarget];
        $data = $params + $data;

        return [Header::Location->value => json_encode($data, JSON_THROW_ON_ERROR)];
    }
}
