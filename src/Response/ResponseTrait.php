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

namespace Webware\Htmx\Response;

use Webware\Htmx\TriggerTrait;

use function json_encode;

/**
 * @api
 */
trait ResponseTrait
{
    use TriggerTrait;

    /** @var list<string> */
    private array $allowedKeys = [
        '',
    ];

    public function htmxLocation(string $path, ?string $target = null): void
    {
        if (null !== $target) {
            $this->headers[Header::Location->value] = json_encode(['path' => $path, 'target' => $target]);

            return;
        }
        $this->headers[Header::Location->value] = $path;
    }
}
