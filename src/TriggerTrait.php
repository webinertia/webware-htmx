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

use Webware\Htmx\Response\Header;

use function json_encode;

/**
 * @api
 */
trait TriggerTrait
{
    final public const string SYSTEM_MESSAGE = 'systemMessage';

    /**
     *
     * @var array<non-empty-string, string|bool>
     */
    protected array $headers = [];

    /**
     * @param array<array-key, mixed> $data
     */
    public function htmxTrigger(
        array $data,
        string $event = self::SYSTEM_MESSAGE,
        Header $header = Header::TriggerAfterSettle,
    ): void {
        $this->headers[$header->value] = json_encode([$event => $data]);
    }
}
