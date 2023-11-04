<?php

declare(strict_types=1);

namespace App\Config;

use NGSOFT\Enums\EnumTrait;

enum Placement: string
{
    use EnumTrait;

    case Auto   = 'auto';
    case Top    = 'top';
    case Bottom = 'bottom';
    case Left   = 'left';
    case Right  = 'right';
}
