<?php

/*
 * This file is part of the zenstruck/memoize package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Memoize;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class Cache
{
    private static self $instance;

    /** @var \WeakMap<object,\ArrayObject<string,mixed>> */
    private \WeakMap $cache;

    private function __construct()
    {
        $this->cache = new \WeakMap();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * @template T
     *
     * @param callable():T $factory
     *
     * @return T
     */
    public function get(object $object, string $key, callable $factory): mixed
    {
        $array = $this->cache[$object] ??= new \ArrayObject();

        if ($array->offsetExists($key)) {
            return $array[$key];
        }

        return $array[$key] = $factory();
    }

    public function clear(object $object, ?string $key = null): void
    {
        if (!isset($this->cache[$object])) {
            return;
        }

        if ($key) {
            unset($this->cache[$object][$key]);

            return;
        }

        unset($this->cache[$object]);
    }
}
