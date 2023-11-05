<?php

declare(strict_types=1);

namespace App\Config;

use NGSOFT\Enums\EnumTrait;

enum ColorType: string implements \JsonSerializable
{
    use EnumTrait;

    case SUCCESS   = 'success';
    case DANGER    = 'danger';
    case WARNING   = 'warning';
    case PRIMARY   = 'primary';
    case SECONDARY = 'secondary';
    case INFO      = 'info';
}
