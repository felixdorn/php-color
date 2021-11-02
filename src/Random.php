<?php

namespace Delight\Color;

use Savvot\Random\MtRand;

class Random
{
    protected static ?MtRand $rnd = null;

    public static function alpha(?string $seed = null): float
    {
        if (self::$rnd === null) {
            self::$rnd = new MtRand($seed);
        }

        self::$rnd->setSeed($seed);

        return round(self::$rnd->randomFloat(), 1);
    }

    public static function between(int $min, int $max, ?string $seed = null): int
    {
        if (self::$rnd === null) {
            self::$rnd = new MtRand($seed);
        }

        self::$rnd->setSeed($seed);

        return self::$rnd->random($min, $max);
    }
}
