<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use App\Config\ColorType;
use App\Utils\OptionManager;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly OptionManager $optionManager) {}

    public function getOption(string $id, mixed $defaultValue = '')
    {
        return $this->optionManager->getItem($id, $defaultValue);
    }

    public function isInstanceOf(mixed $value, string $className): bool
    {
        if ( ! is_object($value))
        {
            return false;
        }

        if (trait_exists($className))
        {
            return in_array($className, class_uses_recursive($value));
        }

        if ( ! class_exists($className))
        {
            return false;
        }

        return is_a($value, $className);
    }

    public function isValidColorType(string $value): bool
    {
        return null !== ColorType::tryFrom($value);
    }
}
