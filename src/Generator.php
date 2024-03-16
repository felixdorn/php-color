<?php

namespace Delight\Color;

class Generator
{
    public static array $defaultHue        = [0, 360];
    public static array $defaultSaturation = [50, 90];
    public static array $defaultLightness  = [50, 70];

    public static function withDefaults(?array $hue = null, ?array $saturation = null, ?array $lightness = null): void
    {
        if ($hue) {
            static::$defaultHue = $hue;
        }

        if ($saturation) {
            static::$defaultSaturation = $saturation;
        }

        if ($lightness) {
            static::$defaultLightness = $lightness;
        }
    }

    public static function many(int $n, ?string $seed = null): array
    {
        return iterator_to_array(self::manyLazily($n, $seed));
    }

    public static function manyLazily(int $n, ?string $seed = null): \Generator
    {
        for ($i = 0; $i < $n; $i++) {
            $uniqueSeed = $seed;

            // This part needs to be deterministic.
            if ($seed) {
                $uniqueSeed .= "_{$i}";
            }

            yield self::one($uniqueSeed);
        }
    }

    public static function one(?string $seed = null): Hsl
    {
        return Hsl::boundedRandom(
            hue: static::$defaultHue,
            saturation: static::$defaultSaturation,
            lightness: static::$defaultLightness,
            seed: $seed
        );
    }
}
