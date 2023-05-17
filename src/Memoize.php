<?php

/*
 * This file is part of the zenstruck/memoize package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck;

use Zenstruck\Memoize\Cache;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait Memoize
{
    /**
     * @template T
     *
     * @param callable():T $factory
     *
     * @return T
     */
    protected function memoize(callable $factory, ?string $key = null): mixed
    {
        if (null === $key) {
            $key = \debug_backtrace(options: \DEBUG_BACKTRACE_IGNORE_ARGS, limit: 2)[1]['function'] ?? null;
        }

        if (!$key) {
            throw new \LogicException('Could not automatically determine memoize key. Pass the key explicitly.');
        }

        return Cache::getInstance()->get($this, $key, $factory);
    }

    protected function clearMemoized(?string $key = null): void
    {
        Cache::getInstance()->clear($this, $key);
    }
}
