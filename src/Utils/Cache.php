<?php

declare(strict_types=1);

namespace App\Utils;

use App\Utils\Facade\InnerFacade;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

/**
 * Caches Models Metadata and some datas.
 * Requires a PSR-6 cache to work.
 */
final class Cache extends Facade
{
    protected static ?string $accessor = CacheItemPoolInterface::class;

    /**
     * Gets an entry from the cache.
     *
     * @param string              $key          the cache key
     * @param null|\Closure|mixed $defaultValue if a closure is provided it will be called and the return value will be used
     * @param int                 $seconds      if a default value is provided it will be cached, that parameter set the number of seconds until the item expires, 0 never expires
     */
    public static function get(string $key, mixed $defaultValue = null, int $seconds = 0): mixed
    {
        try
        {
            $item = self::getRoot()->getItem($key);

            if ( ! $item->isHit())
            {
                $value = value($defaultValue);

                if (isset($value))
                {
                    self::set($key, $value, $seconds);
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
    public static function set(string $key, mixed $value, int $seconds = 0): void
    {
        if (null === $value = value($value))
        {
            try
            {
                self::getRoot()->deleteItem($key);
            } catch (InvalidArgumentException)
            {
            }
        } else
        {
            try
            {
                $item = self::getCachePool()
                    ->getItem($key)
                    ->set($value)
                ;

                if ($seconds > 0)
                {
                    $item->expiresAfter($seconds);
                }

                self::getCachePool()->save(
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
    public static function delete(string $key): void
    {
        try
        {
            self::getRoot()->deleteItem($key);
        } catch (InvalidArgumentException)
        {
        }
    }

    /**
     * Clears the cache.
     */
    public static function clear(): void
    {
        self::getRoot()->clear();
    }

    protected static function getDefinitions(InnerFacade $services): array
    {
        // only called if symfony does not have a cache pool
        $adapters   = [new ArrayAdapter(storeSerialized: false)];

        if (ApcuAdapter::isSupported() && 'cli' !== php_sapi_name())
        {
            $adapters[] = new ApcuAdapter();
        }

        $adapters[] = new PhpFilesAdapter();

        return [CacheItemPoolInterface::class => new ChainAdapter($adapters)];
    }
}
