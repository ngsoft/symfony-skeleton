<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use App\Utils\OptionManager;
use Twig\Extension\RuntimeExtensionInterface;

class AppOptionsRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly OptionManager $optionManager) {}

    public function getOption(string $id, mixed $defaultValue = '')
    {
        return $this->optionManager->getItem($id, $defaultValue);
    }
}
