<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Option;

class Options extends Facade
{
    protected static ?string $accessor = OptionManager::class;

    /**
     * Register a new Option.
     */
    public static function register(string $name, mixed $defaultValue, string $description = '', bool $autoload = true): void
    {
        self::getRoot()->register($name, $defaultValue, $description, $autoload);
    }

    /**
     * Get value only if registered.
     */
    public static function get(string $name, mixed $defaultValue = null): mixed
    {
        return self::getRoot()->getItem($name, $defaultValue);
    }

    /**
     * Save value Only if registered.
     */
    public static function set(string $name, mixed $value): void
    {
        self::getRoot()->setItem($name, $value);
    }

    /**
     * Remove an option.
     */
    public static function remove(string $name): void
    {
        self::getRoot()->removeItem($name);
    }

    /**
     * Checks if option exists.
     */
    public static function has(string $name): bool
    {
        return self::getRoot()->hasItem($name);
    }
}
