<?php

namespace Delight\Color;

class Generator
{
    /** @var array{0:int<0,360>, 1:int<0,360>} $defaultHue */
    public static array $defaultHue        = [0, 360];

    /** @var array{0:int<0,100>, 1:int<0,100>} $defaultSaturation */
    public static array $defaultSaturation = [50, 90];

    /** @var array{0:int<0,100>, 1:int<0,100>} $defaultLightness */
    public static array $defaultLightness  = [50, 70];

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|null $lightness
     * @return void
     */
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

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|null $lightness
     * @return Hsl[]
     */
    public static function many(int $n, ?string $seed = null,  ?array $hue = null, ?array $saturation = null, ?array $lightness = null): array
    {
        return iterator_to_array(self::manyLazily($n, $seed, $hue, $saturation, $lightness));
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|null $lightness
     * @return \Generator<Hsl>
     */
    public static function manyLazily(int $n, ?string $seed = null,  ?array $hue = null, ?array $saturation = null, ?array $lightness = null): \Generator
    {
        for ($i = 0; $i < $n; $i++) {
            $uniqueSeed = $seed;

            // This part needs to be deterministic.
            if ($seed) {
                $uniqueSeed .= "_{$i}";
            }

            yield self::one($uniqueSeed, $hue, $saturation, $lightness);
        }
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|null $lightness
     */
    public static function one(?string $seed = null, ?array $hue = null, ?array $saturation = null, ?array $lightness = null): Hsl
    {
        return Hsl::boundedRandom(
            hue: $hue ?? static::$defaultHue,
            saturation: $saturation ?? static::$defaultSaturation,
            lightness: $lightness ?? static::$defaultLightness,
            seed: $seed
        );
    }
}
