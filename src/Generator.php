<?php

namespace Delight\Color;

class Generator
{
    /** @var array{0:int<0,360>, 1:int<0,360>} */
    protected static array $defaultHue        = [0, 360];

    /** @var array{0:int<0,100>, 1:int<0,100>} */
    protected static array $defaultSaturation = [50, 90];

    /** @var array{0:int<0,100>, 1:int<0,100>} */
    protected static array $defaultLightness  = [50, 70];

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

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360>|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $lightness
     */
    public static function withDefaults(array|int|null $hue = null, array|int|null $saturation = null, array|int|null $lightness = null): void
    {
        if (is_int($hue)) {
            $hue = [$hue, $hue];
        }

        if (is_int($saturation)) {
            $saturation = [$saturation, $saturation];
        }

        if (is_int($lightness)) {
            $lightness = [$lightness, $lightness];
        }

        /* @phpstan-ignore smaller.alwaysFalse, greater.alwaysFalse, booleanOr.leftAlwaysFalse */
        if ($hue && (count($hue) != 2 || $hue[0] > $hue[1] || $hue[1] > 360 || $hue[0] < 0)) {
            throw new \UnexpectedValueException('The hue must be an array of the form [min, max] where min > 0 and max <= 360 and min <= max');
        }

        /* @phpstan-ignore smaller.alwaysFalse, greater.alwaysFalse, booleanOr.leftAlwaysFalse */
        if ($saturation && (count($saturation) != 2 || $saturation[0] > $saturation[1] || $saturation[1] > 100 || $saturation[0] < 0)) {
            throw new \UnexpectedValueException('The saturation must be an array of the form [min, max] where min > 0 and max <= 100 and min <= max');
        }

        /* @phpstan-ignore smaller.alwaysFalse, greater.alwaysFalse, booleanOr.leftAlwaysFalse */
        if ($lightness && (count($lightness) != 2 || $lightness[0] > $lightness[1] || $lightness[1] > 100 || $lightness[0] < 0)) {
            throw new \UnexpectedValueException('The lightness must be an array of the form [min, max] where min > 0 and max <= 100 and min <= max');
        }

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
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360>|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $lightness
     *
     * @return Hsl[]
     */
    public static function many(int $n, ?string $seed = null, array|int|null $hue = null, array|int|null $saturation = null, array|int|null $lightness = null): array
    {
        return iterator_to_array(self::manyLazily($n, $seed, $hue, $saturation, $lightness));
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360>|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $lightness
     *
     * @return \Generator<Hsl>
     */
    public static function manyLazily(int $n, ?string $seed = null, array|int|null $hue = null, array|int|null $saturation = null, array|int|null $lightness = null): \Generator
    {
        for ($i = 0; $i < $n; $i++) {
            $uniqueSeed = $seed;

            // This part needs to be deterministic.
            // Changing this is a breaking change.
            if ($seed) {
                $uniqueSeed .= "_{$i}";
            }

            yield self::one($uniqueSeed, $hue, $saturation, $lightness);
        }
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360>|null $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $lightness
     */
    public static function one(?string $seed = null, array|int|null $hue = null, array|int|null $saturation = null, array|int|null $lightness = null): Hsl
    {
        return Hsl::boundedRandom(
            hue: $hue ?? static::$defaultHue,
            saturation: $saturation ?? static::$defaultSaturation,
            lightness: $lightness ?? static::$defaultLightness,
            seed: $seed
        );
    }

    /***
     * @param int $n
     * @param string|null $seed
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $saturation
     * * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100>|null $lightness
     * @return \Generator<Hsl>
     */
    public static function maximizeHueDifferenceForConsecutiveColors(int $n, ?string $seed = null, array|int|null $saturation = null, array|int|null $lightness = null): \Generator {
        $color = static::one($seed, 0, $saturation, $lightness);

        if ($n <= 0) {
            throw new \InvalidArgumentException("The number of colors must be greater than 0.");
        }

        $start = 0;
        $end = $n - 1;

        $angle = 360 / $n;

        while ($start <= $end) {
            yield $color->withHue($angle * $end);
            $end--;

            if ($start <= $end) {
                yield $color->withHue($angle * $start);
                $start++;
            }
        }
    }
}
