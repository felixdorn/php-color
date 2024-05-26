<?php

namespace Delight\Color;

class Hsl
{
    public const int IS_BRIGHT_THRESHOLD = 90;
    public const int IS_DARK_THRESHOLD   = 15;

    public float $hue;
    public float $saturation;
    public float $lightness;

    // This is private because it does not handle alphas neatly and requires
    // to check whether the first capture group has a length of 5 or 7 to validate it.
    private const string HEX_REGEX = "/^\#?
        ([\da-fA-F]{1,8})
    $/x";
    public const string RGB_CSS_REGEX = "/rgba?\(
        ([\d.]+)\s*,\s*
        ([\d.]+)\s*,\s*
        ([\d.]+)\s*,?\s*
    /x";
    public const string HSL_CSS_REGEX = "/hsla?\(
        ([\d.]+)\s*,\s*
        ([\d.]+)%?\s*,\s*
        ([\d.]+)%?\s*,?\s*
    /x";

    /**
     * @param float $hue        between 0 and 360
     * @param float $saturation between 0 and 100
     * @param float $lightness  between 0 and 100
     */
    public function __construct(float $hue, float $saturation, float $lightness)
    {
        // People can do the necessary angle conversion if needed, let's be dumb.
        if ((int) $hue > 360 || (int) $hue < 0) {
            throw new \InvalidArgumentException("Hue must be between 0 and 360, got: {$hue}");
        }

        if ((int) $saturation > 100 || (int) $saturation < 0) {
            throw new \InvalidArgumentException("Saturation must be between 0 and 100, got: {$saturation}");
        }

        if ((int) $lightness > 100 || (int) $lightness < 0) {
            throw new \InvalidArgumentException("Lightness must be between 0 and 100, got: {$lightness}");
        }

        // If we get obvious percentages, convert them to our 0-100 scale.
        if ($saturation > 0 && $saturation <= 1 && $lightness > 0 && $lightness <= 1) {
            $saturation *= 100;
            $lightness *= 100;
        }

        $this->hue        = $hue;
        $this->saturation = $saturation;
        $this->lightness  = $lightness;
    }

    public static function random(?string $seed = null): Hsl
    {
        return static::boundedRandom([0, 360], [0, 100], [0, 100], $seed);
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360> $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100> $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100> $lightness
     */
    public static function boundedRandom(array|int $hue, array|int $saturation, array|int $lightness, ?string $seed = null): Hsl
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
        if (count($hue) != 2 || $hue[0] > $hue[1] || $hue[1] > 360 || $hue[0] < 0) {
            throw new \UnexpectedValueException('The hue must be an array of the form [min, max] where min > 0 and max <= 360 and min <= max');
        }

        /* @phpstan-ignore smaller.alwaysFalse, greater.alwaysFalse, booleanOr.leftAlwaysFalse */
        if (count($saturation) != 2 || $saturation[0] > $saturation[1] || $saturation[1] > 100 || $saturation[0] < 0) {
            throw new \UnexpectedValueException('The saturation must be an array of the form [min, max] where min > 0 and max <= 100 and min <= max');
        }

        /* @phpstan-ignore smaller.alwaysFalse, greater.alwaysFalse, booleanOr.leftAlwaysFalse */
        if (count($lightness) != 2 || $lightness[0] > $lightness[1] || $lightness[1] > 100 || $lightness[0] < 0) {
            throw new \UnexpectedValueException('The lightness must be an array of the form [min, max] where min > 0 and max <= 100 and min <= max');
        }

        return new self(
            Random::between($hue[0], $hue[1], $seed),
            Random::between($saturation[0], $saturation[1], $seed),
            Random::between($lightness[0], $lightness[1], $seed),
        );
    }

    /**
     * Create an HSL object from a CSS-like string. Variants with transparency, e.g., rgba, are not supported.
     */
    public static function fromString(string $color): Hsl
    {
        if (preg_match(self::HEX_REGEX, $color, $matches) && !in_array(strlen($matches[1]), [5, 7])) {
            return self::fromHex($color);
        }

        if (preg_match(static::RGB_CSS_REGEX, $color, $matches)) {
            return self::fromRGB((int) $matches[1], (int) $matches[2], (int) $matches[3]);
        }

        if (preg_match(static::HSL_CSS_REGEX, $color, $matches)) {
            // (float) will convert ".1" to "0.1"
            return new Hsl((float) $matches[1], (float) $matches[2], (float) $matches[3]);
        }

        throw new \InvalidArgumentException("The argument color expects a CSS-like string with either a hex code or a rgb, rgba, hsl, hsla function; got, `{$color}`");
    }

    public static function fromHex(string $hex): Hsl
    {
        if (!preg_match(self::HEX_REGEX, $hex, $matches) || in_array(strlen($matches[1]), [5, 7])) {
            throw new \InvalidArgumentException("Invalid hex value `{$hex}` ");
        }

        if (str_starts_with($hex, '#')) {
            $hex = substr($hex, 1);
        }

        $size = strlen($hex);
        if ($size < 5) {
            // Repeat each character 6/n where n is the size of the string
            // Ex: #ab -> #a__b__ -> #aaabbb
            // Ex: #abc -> #a_b_c_ -> #aabbcc
            // Ex: #a --> #a_____ -> #aaaaaa
            $expanded = array_map(
                fn ($c) => str_repeat($c, (int) round(6 / $size)),
                str_split($hex)
            );
            $hex = implode('', $expanded);
        }

        $hex = substr($hex, 0, 6);

        [$r, $g, $b] = array_map('hexdec', str_split($hex, 2));

        return static::fromRGB((int) $r, (int) $g, (int) $b);
    }

    /**
     * @see {https://stackoverflow.com/a/39147465}
     */
    public static function fromRGB(int $r, int $g, int $b): Hsl
    {
        $r = (float) $r;
        $g = (float) $g;
        $b = (float) $b;

        $r /= 255;
        $g /= 255;
        $b /= 255;

        $channelMax = max($r, $g, $b);
        $channelMin = min($r, $g, $b);
        $delta      = $channelMax - $channelMin;
        $hue        = 0;
        $lightness  = ($channelMax + $channelMin) / 2;

        if ($delta === 0.0) {
            return new Hsl(0, 0, round($lightness * 100, 1));
        }

        if ($channelMax === $r) {
            // PHP mod operator always returns an integer.
            $hue = fmod(($g - $b) / $delta, 6);
        } elseif ($channelMax === $g) {
            $hue = (($b - $r) / $delta) + 2;
        } elseif ($channelMax === $b) {
            $hue = (($r - $g) / $delta) + 4;
        }

        $hue = round($hue * 60, 1);

        if ($hue < 0) {
            $hue += 360;
        }

        $saturation = ($delta / (1 - abs(2 * $lightness - 1)));

        return new Hsl(
            $hue,
            round($saturation * 100, 1),
            round($lightness * 100, 1)
        );
    }

    /** @return int<0,255> */
    public function red(): int
    {
        return $this->colorChannels()[0];
    }

    /**
     * @see {https://en.wikipedia.org/wiki/HSL_and_HSV#HSL_to_RGB_alternative} for implementation details
     *
     * @return array{0: int<0, 255>, 1: int<0,255>, 2: int<0,255>}
     */
    public function colorChannels(): array
    {
        // This code is not meant to be understood immediately, see the link above for context.
        $s = $this->saturation / 100;
        $l = $this->lightness / 100;

        $a = $s * min($l, 1 - $l);
        $f = function ($n) use ($l, $a) {
            $k = fmod($n + $this->hue / 30, 12);

            return $l - $a * max(-1, min($k - 3, 9 - $k, 1));
        };

        /* @phpstan-ignore-next-line PHPStan can't see that the ints are bounded between 0-255, it's fine. */
        return [(int) round($f(0) * 255), (int) round($f(8) * 255), (int) round($f(4) * 255)];
    }

    /** @return string Returns the CSS representation of the equivalent color in RGB: rgb(r, g, b)   */
    public function toRgb(): string
    {
        return sprintf('rgb(%d, %d, %d)', $this->red(), $this->green(), $this->blue());
    }

    /** @return string Returns the CSS representation of the HSL color: hsl(h, s%, l%) */
    public function toHsl(): string
    {
        return sprintf(
            'hsl(%s, %s, %s)',
            $this->hue,
            $this->saturation . '%',
            $this->lightness . '%'
        );
    }

    /* @param $threshold int A number between 0 and 100, 0 is darkest, 100 is brightest. */
    public function isDark(int $threshold = self::IS_DARK_THRESHOLD): bool
    {
        return $this->lightness <= $threshold;
    }

    /* @param $threshold int A number between 0 and 100, 0 is darkest, 100 is brightest. */
    public function isBright(int $threshold = self::IS_BRIGHT_THRESHOLD): bool
    {
        return $this->lightness >= $threshold;
    }

    /* @param int $percentage A number between 0 and 100, for a given percentage n, the color will be darkened by n% */
    public function darken(int $percentage): self
    {
        return $this->lighten(-$percentage);
    }

    /* @param int $percentage A number between -100 and 100, for a given percentage n, the color will be lightened by n%. A negative percentage means darken. */
    public function lighten(int $percentage): self
    {
        return new self(
            $this->hue,
            $this->saturation,
            min(100, max(0, $this->lightness + $percentage))
        );
    }

    /** @return string Returns the CSS representation of the HSL color: hsl(h, s%, l%) */
    public function __toString(): string
    {
        return $this->toHsl();
    }

    /** @return string Returns the CSS representation of the equivalent color in HEX: #rrggbb   */
    public function toHex(): string
    {
        $rgb = $this->colorChannels();

        $red   =  str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT);
        $green =  str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT);
        $blue  =  str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);

        return '#' . $red . $green . $blue;
    }

    /** @return int<0,255> */
    public function blue(): int
    {
        return $this->colorChannels()[2];
    }

    /** @return int<0,255> */
    public function green(): int
    {
        return $this->colorChannels()[1];
    }

    /**
     * @see {https://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef}
     *
     * @return float between 0.0 and 21.0
     */
    public function contrast(Hsl $color): float
    {
        $original = $this->luminance();
        $against  = $color->luminance();

        $brightest = max($original, $against) + 0.05;
        $darkest   = min($original, $against) + 0.05;

        return $brightest / $darkest;
    }

    /**
     * @see {https://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef}
     *
     * @return float between 0.0 and 1.0
     */
    public function luminance(): float
    {
        [$R, $G, $B] = array_map(function ($channel) {
            // Normalized to 0 for darkest black and 1 for lightest white
            $channel /= 255;

            return $channel <= 0.03928 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4;
        }, $this->colorChannels());

        return $R * 0.2126 + $G * 0.7152 + $B * 0.0722;
    }

    /** @return string Returns the CSS representation of the HSL color: hsl(h, s%, l%) */
    public function toString(): string
    {
        return (string) $this;
    }
}
