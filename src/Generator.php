<?php

namespace Felix\PHPColor;

class Generator
{
    /** @var array{0:int<0,360>, 1:int<0,360>} */
    protected static array $defaultHue        = [0, 360];

    /** @var array{0:int<0,100>, 1:int<0,100>} */
    protected static array $defaultSaturation = [50, 90];

    /** @var array{0:int<0,100>, 1:int<0,100>} */
    protected static array $defaultLightness  = [50, 70];

    /** @var array{0:int<0,100>, 1:int<0,100>} */
    protected static array $defaultAlpha = [100, 100];

    /** @return array{0:int<0,360>, 1:int<0,360>} */
    public static function defaultHue(): array
    {
        return static::$defaultHue;
    }

    /** @return  array{0:int<0,100>, 1:int<0,100>} */
    public static function defaultSaturation(): array
    {
        return static::$defaultSaturation;
    }

    /** @return  array{0:int<0,100>, 1:int<0,100>} */
    public static function defaultLightness(): array
    {
        return static::$defaultLightness;
    }

    /** @return  array{0:int<0,100>, 1:int<0,100>} */
    public static function defaultAlpha(): array
    {
        return static::$defaultAlpha;
    }


    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360>|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $lightness
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $alpha
     */
    public static function withDefaults(array|int|null $hue = null, array|int|null $saturation = null, array|int|null $lightness = null, array|int|null $alpha = null): void
    {
        $hsla = Hsla::normalizeRange($hue ?? static::$defaultHue, $saturation ?? static::$defaultSaturation, $lightness ?? static::$defaultLightness, $alpha ?? static::$defaultAlpha);

        static::$defaultHue        = $hsla[0];
        static::$defaultSaturation = $hsla[1];
        static::$defaultLightness  = $hsla[2];
        static::$defaultAlpha      = $hsla[3];
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360>|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $lightness
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $alpha
     *
     * @return Hsla[]
     */
    public static function many(int $n, ?string $seed = null, array|int|null $hue = null, array|int|null $saturation = null, array|int|null $lightness = null, array|int|null $alpha = null): array
    {
        return iterator_to_array(self::manyLazily($n, $seed, $hue, $saturation, $lightness, $alpha));
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360>|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $lightness
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $alpha
     *
     * @return \Generator<Hsla>
     */
    public static function manyLazily(int $n, ?string $seed = null, array|int|null $hue = null, array|int|null $saturation = null, array|int|null $lightness = null, array|int|null $alpha = null): \Generator
    {
        for ($i = 0; $i < $n; $i++) {
            $uniqueSeed = $seed;

            // This part needs to be deterministic.
            // Changing this is a breaking change.
            if ($seed) {
                $uniqueSeed .= "_{$i}";
            }

            yield self::one($uniqueSeed, $hue, $saturation, $lightness, $alpha);
        }
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360>|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $lightness
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $alpha
     */
    public static function one(?string $seed = null, array|int|null $hue = null, array|int|null $saturation = null, array|int|null $lightness = null, array|int|null $alpha = null): Hsla
    {
        return Hsla::boundedRandom(
            hue: $hue ?? static::$defaultHue,
            saturation: $saturation ?? static::$defaultSaturation,
            lightness: $lightness ?? static::$defaultLightness,
            alpha: $alpha ?? static::$defaultAlpha,
            seed: $seed
        );
    }
}
