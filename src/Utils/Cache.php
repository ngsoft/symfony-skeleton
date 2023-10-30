<?php

declare(strict_types=1);

namespace App\Utils;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

final class Cache
{
    public function __construct(private readonly CacheItemPoolInterface $cache) {}

    /**
     * Gets an entry from the cache.
     *
     * @param string              $key          the cache key
     * @param null|\Closure|mixed $defaultValue if a closure is provided it will be called and the return value will be used
     * @param int                 $seconds      if a default value is provided it will be cached, that parameter set the number of seconds until the item expires, 0 never expires
     */
    public function get(string $key, mixed $defaultValue = null, int $seconds = 0): mixed
    {
        try
        {
            $item = $this->cache->getItem($key);

            if ( ! $item->isHit())
            {
                $value = value($defaultValue);

                if (isset($value))
                {
                    $this->set($key, $value, $seconds);
                }
            } else
            {
                $value = $item->get();
            }

            return $value;
        } catch (InvalidArgumentException)
        {
        }

        return value($defaultValue);
    }

    /**
     * Saves a value into the cache.
     *
     * @param string $key     The cache key
     * @param mixed  $value   the value to store, null will delete the cached item
     * @param int    $seconds that parameters give the number of seconds until the item expires, 0 never expires
     */
    public function set(string $key, mixed $value, int $seconds = 0): void
    {
        if (null === $value = value($value))
        {
            try
            {
                $this->cache->deleteItem($key);
            } catch (InvalidArgumentException)
            {
            }
        } else
        {
            try
            {
                $item = $this->cache
                    ->getItem($key)
                    ->set($value)
                ;

                if ($seconds > 0)
                {
                    $item->expiresAfter($seconds);
                }

                $this->cache->save(
                    $item
                );
            } catch (InvalidArgumentException)
            {
            }
        }
    }

    /**
     * Removes a value from the cache.
     */
    public function delete(string $key): void
    {
        try
        {
            $this->cache->deleteItem($key);
        } catch (InvalidArgumentException)
        {
        }
    }

    /**
     * Clears the cache.
     */
    public function clear(): void
    {
        $this->cache->clear();
    }

    /**
     * Generates a cache key from concatenated values.
     */
    public function makeCacheKey(mixed $value, mixed ...$values): string
    {
        array_unshift($values, $value);
        $result = [];

        foreach ($values as $value)
        {
            if (is_object($value))
            {
                if ( ! is_stringable($value))
                {
                    $value = get_class($value);
                } else
                {
                    $value = str_val($value);
                }
            } elseif (is_scalar($value))
            {
                $value = str_val($value);
            }

            if (is_string($value))
            {
                $result[] = $value;
            }
        }

        if (empty($result))
        {
            throw new \ValueError('Cache key cannot be empty');
        }
        return sha1(
            implode(
                ':',
                $result
            )
        );
    }
}
