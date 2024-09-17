<?php

namespace Felix\PHPColor;

use Savvot\Random\MtRand;

class Random
{
    protected static ?MtRand $rnd = null;

    public static function between(int $min, int $max, ?string $seed = null): int
    {
        if (self::$rnd === null) {
            self::$rnd = new MtRand($seed);
        }

        self::$rnd->setSeed($seed);

        return self::$rnd->random($min, $max);
    }
}
