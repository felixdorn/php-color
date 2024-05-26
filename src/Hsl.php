<?php

namespace Delight\Color;

class Hsl
{
    public const int BRIGHTNESS_THRESHOLD = 90;
    public const int DARKNESS_THRESHOLD   = 15;
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
        if ($saturation > 0 && $saturation < 1) {
            $saturation *= 100;
        }

        if ($lightness > 0 && $lightness < 1) {
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
     * @param array{0: int<0, 360>, 1: int<0, 360>} $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>} $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>} $lightness
     * @param string|null $seed
     * @return Hsl
     */
    public static function boundedRandom(array $hue, array $saturation, array $lightness, ?string $seed = null): Hsl
    {
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
    public static function fromRGB(float $r, float $g, float $b): Hsl
    {
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

    public function red(): int
    {
        return $this->colorChannels()[0];
    }

    /**
     * @see {https://en.wikipedia.org/wiki/HSL_and_HSV#HSL_to_RGB_alternative} for implementation details
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

        /** @phpstan-ignore-next-line PHPStan can't see that the ints are bounded between 0-255, it's fine. */
        return [(int) round($f(0) * 255), (int) round($f(8) * 255), (int) round($f(4) * 255)];
    }

    public function toRgb(): string
    {
        return sprintf('rgb(%d, %d, %d)', $this->red(), $this->green(), $this->blue());
    }

    public function toHsl(): string
    {
        return sprintf('hsl(%f%%, %f%%, %f%%)', $this->hue, $this->saturation, $this->lightness);
    }

    public function isDark(): bool
    {
        return $this->lightness <= static::DARKNESS_THRESHOLD;
    }

    public function isBright(): bool
    {
        return $this->lightness >= static::BRIGHTNESS_THRESHOLD;
    }

    public function darken(int $percentage): self
    {
        return $this->lighten(-$percentage);
    }

    public function lighten(int $percentage): self
    {
        return new self(
            $this->hue,
            $this->saturation,
            min(100, max(0, $this->lightness + $percentage))
        );
    }

    public function __toString(): string
    {
        return sprintf(
            'hsl(%s,%s,%s)',
            $this->hue,
            $this->saturation . '%',
            $this->lightness . '%'
        );
    }

    public function toHex(): string
    {
        $rgb = $this->colorChannels();

        $red   =  str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT);
        $green =  str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT);
        $blue  =  str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);

        return '#' . $red . $green . $blue;
    }

    public function blue(): int
    {
        return $this->colorChannels()[2];
    }

    public function green(): int
    {
        return $this->colorChannels()[1];
    }

    /**
     * @see {https://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef}
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
     */
    public function luminance(): float
    {
        [$R, $G, $B] = array_map(function ($channel) {
            // Normalized to 0 for darkest black and 1 for lightest white
            $channel /= 255;

            return $channel <= 0.03928 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4;
        }, $this->colorChannels());

        $luminance = $R * 0.02126 + $G * 0.7152 + $B * 0.0722;

        // Normalized to return a value between 0 and 1
        return $luminance / (0.02126 + 0.7152 + 0.0722);
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
