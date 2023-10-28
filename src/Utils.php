<?php

declare(strict_types=1);

namespace App;

class Utils
{
    public static function toJsonDateString(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d') . 'T' . $date->format('H:i:s.u') . 'Z';
    }
}
