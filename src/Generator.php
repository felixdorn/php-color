<?php

namespace Delight\Color;

class Generator
{
    public static function many(int $n, ?string $seed = null): array
    {
        return iterator_to_array(self::manyLazily($n, $seed));
    }

    public static function manyLazily(int $n, ?string $seed = null): \Generator
    {
        for ($i = 0; $i < $n; $i++) {
            yield self::one($seed);
        }
    }

    public static function one(string $seed = null): Hsl
    {
        return Hsl::limitedRandom(
            hue: [0, 360],
            saturation: [50, 90],
            lightness: [50, 70]
        );
    }
}
