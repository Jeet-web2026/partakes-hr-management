<?php

namespace App\Services\Common;

class UinGeneratorService
{
    public static function generate(): int
    {
        // Mirrors format seen in dump: microtime-based prefix + random suffix, ~11 digits
        $micro = (int) (microtime(true) * 1000) % 100000;
        $rand = random_int(100, 999);

        return (int) (time() . $micro . $rand) % 99999999999;
    }
}
