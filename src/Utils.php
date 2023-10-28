<?php

declare(strict_types=1);

namespace App;

class Utils
{
    public static function toJsonDateString(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d') . 'T' . $date->format('H:i:s.u') . 'Z';
    }

    public static function capitalize(string $text): string
    {
        return ucfirst(
            preg_replace_callback(
                '#(\h+)([a-z])#',
                fn ($matches) => $matches[1] . ucfirst($matches[2]),
                mb_strtolower($text)
            )
        );
    }
}
