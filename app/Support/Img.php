<?php

namespace App\Support;

class Img
{
    public static function url(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : null;
    }
}
