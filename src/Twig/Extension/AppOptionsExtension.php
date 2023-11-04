<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\AppOptionsRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppOptionsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('option_get', [AppOptionsRuntime::class, 'getOption'], ['is_safe' => ['html']]),
        ];
    }
}
