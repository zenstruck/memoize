# zenstruck/memoize

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
        return $this->memoize(
            __FUNCTION__, // memoize requires a "cache key" an easy choice is the function name
            fn() => $this->someExpensiveOperation() // called only the first time method1() is called
        );
    }

    public function method2(string $parameter): mixed
    {
        return $this->memoize(
            __FUNCTION__.$parameter, // cache key includes the parameter
            fn() => $this->someExpensiveOperation($parameter) // called once per unique parameter
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
