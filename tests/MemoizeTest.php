<?php

/*
 * This file is part of the zenstruck/memoize package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Memoize;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MemoizeTest extends TestCase
{
    /**
     * @test
     */
    public function can_memoize_value(): void
    {
        $object = new DummyObject();
        $factory = new Factory(static fn() => \random_int(1, 100000));

        $initial = $object->memoize('key1', $factory);

        $this->assertSame(1, $factory->calls);
        $this->assertIsInt($initial);
        $this->assertSame($initial, $object->memoize('key1', $factory));
        $this->assertSame($initial, $object->memoize('key1', $factory));
        $this->assertSame($initial, $object->memoize('key1', $factory));
        $this->assertSame(1, $factory->calls);
        $this->assertNotSame($initial, $new = $object->memoize('key2', $factory));
        $this->assertSame($new, $object->memoize('key2', $factory));
        $this->assertSame(2, $factory->calls);
    }

    /**
     * @test
     */
    public function can_memoize_null(): void
    {
        $object = new DummyObject();
        $factory = new Factory(static fn() => null);

        $this->assertNull($object->memoize('key', $factory));
        $this->assertNull($object->memoize('key', $factory));
        $this->assertNull($object->memoize('key', $factory));
        $this->assertNull($object->memoize('key', $factory));
        $this->assertSame(1, $factory->calls);
    }

    /**
     * @test
     */
    public function can_clear_single_key(): void
    {
        $object = new DummyObject();
        $factory = new Factory(static fn() => \random_int(1, 100000));

        $initial1 = $object->memoize('key1', $factory);
        $initial2 = $object->memoize('key2', $factory);

        $object->clearMemoized('key1');

        $this->assertNotSame($initial1, $object->memoize('key1', $factory));
        $this->assertSame($initial2, $object->memoize('key2', $factory));
    }

    /**
     * @test
     */
    public function can_clear_object(): void
    {
        $object = new DummyObject();
        $factory = new Factory(static fn() => \random_int(1, 100000));

        $initial1 = $object->memoize('key1', $factory);
        $initial2 = $object->memoize('key2', $factory);

        $object->clearMemoized();
        $object->clearMemoized(); // call twice to ensure "nothing happens" when clearing an unset object

        $this->assertNotSame($initial1, $object->memoize('key1', $factory));
        $this->assertNotSame($initial2, $object->memoize('key2', $factory));
    }
}

class Factory
{
    public int $calls = 0;

    public function __construct(private \Closure $factory)
    {
    }

    public function __invoke(): mixed
    {
        ++$this->calls;

        return ($this->factory)();
    }
}

class DummyObject
{
    use Memoize {
        memoize as public;
        clearMemoized as public;
    }
}
