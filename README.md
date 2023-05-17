# zenstruck/memoize

[![CI](https://github.com/zenstruck/memoize/actions/workflows/ci.yml/badge.svg)](https://github.com/zenstruck/memoize/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/zenstruck/memoize/branch/1.x/graph/badge.svg?token=ZQPY6GSxvt)](https://codecov.io/gh/zenstruck/memoize)

Helper trait to efficiently cache expensive methods in memory.

## Installation

```bash
composer require zenstruck/memoize
```

## Usage

Add the memoize trait to an object you wish to cache operations on.

```php
use Zenstruck\Memoize;

class MyObject
{
    use Memoize;

    public function method1(): mixed
    {
        // cache key defaults to the method name "method1"
        return $this->memoize(
            fn() => $this->someExpensiveOperation() // called only the first time method1() is called
        );
    }

    public function method2(): mixed
    {
        return $this->memoize(
            fn() => $this->someExpensiveOperation(),
            'my_custom_cache_key' // explicitly set the cache key
        );
    }

    public function method3(string $parameter): mixed
    {
        return $this->memoize(
            fn() => $this->someExpensiveOperation($parameter) // called once per unique parameter
            'my_custom_cache_key'.$parameter, // cache key includes the parameter
        )
    }

    public function refresh(): void
    {
        $this->clearMemoized(); // clear all cached values for this object instance
        $this->clearMemoized('method1'); // clear just the cached value for "method1"
    }
}
```

> **Note**: The cached values are stored in a `WeakMap` keyed by each object's instance. They are
> automatically cleared as the objects are garbage collected.
