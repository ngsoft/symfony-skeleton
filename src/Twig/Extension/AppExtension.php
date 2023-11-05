<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\AppRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('option_get', [AppRuntime::class, 'getOption'], ['is_safe' => 'html']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('option_get', [AppRuntime::class, 'getOption'], ['is_safe' => ['html']]),
            new TwigFunction('instanceof', [AppRuntime::class, 'isInstanceOf']),
            new TwigFunction('valid_color', [AppRuntime::class, 'isValidColorType']),
        ];
    }
}
