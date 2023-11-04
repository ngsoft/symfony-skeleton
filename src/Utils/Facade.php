<?php

declare(strict_types=1);

namespace App\Utils;

use App\Utils\Facade\InnerFacade;

abstract class Facade
{
    /**
     * Override accessor there.
     */
    protected static ?string $accessor = null;

    private function __construct() {}

    /**
     * Handle dynamic, static calls to the object.
     */
    final public static function __callStatic(string $name, array $arguments): mixed
    {
        $obj = static::getRoot();

        if ( ! method_exists($obj, $name))
        {
            throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $name));
        }

        return $obj->{$name}(...$arguments);
    }

    /**
     * This is the name of the service to use for the facade.
     */
    protected static function getAccessor(): string
    {
        return static::$accessor ?? class_basename(static::class);
    }

    /**
     * You must implement that method in your facade to set your custom definitions.
     */
    protected static function getDefinitions(InnerFacade $services): array
    {
        return [];
    }

    final protected static function getRoot(): object
    {
        if (__CLASS__ === static::class)
        {
            return self::getFacade();
        }

        return self::resolveRoot(static::getAccessor());
    }

    private static function resolveRoot(string $accessor): object
    {
        if ( ! self::getFacade()->hasService($accessor))
        {
            self::getFacade()->addServices(
                static::getDefinitions(
                    self::getFacade()
                )
            );
        }

        return self::getFacade()->getService($accessor);
    }

    private static function getFacade(): InnerFacade
    {
        static $inner;
        return $inner ??= new InnerFacade();
    }
}
