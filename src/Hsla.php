<?php

namespace Felix\PHPColor;

class Hsla
{
    public const int IS_BRIGHT_THRESHOLD = 90;
    public const int IS_DARK_THRESHOLD   = 15;

    public float $hue;
    public float $saturation;
    public float $lightness;
    public float $alpha;

    protected const string HEX_REGEX = "/
        \#?
        ([\da-fA-F]{1,8})
    /x";

    protected const string RGB_CSS_REGEX = "/^rgba?\(
        ([\d.]+)\s*,\s*
        ([\d.]+)\s*,\s*
        ([\d.]+)\s*,?\s*
    $/x";

    protected const string HSL_CSS_REGEX = "/hsla?\(
        ([\d.]+)\s*,\s*
        ([\d.]+)%?\s*,\s*
        ([\d.]+)%?\s*,?\s*
    /x";

    /**
     * @param float|int $hue        between 0 and 360
     * @param float|int $saturation between 0 and 100
     * @param float|int $lightness  between 0 and 100
     */
    public function __construct(int|float $hue, float|int $saturation, float|int $lightness, float|int $alpha)
    {
        $this->setHue($hue);
        $this->setSaturation($saturation);
        $this->setLightness($lightness);
        $this->setAlpha($alpha);
    }

    public static function random(?string $seed = null): Hsla
    {
        return static::boundedRandom([0, 360], [0, 100], [0, 100], [0, 100], $seed);
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360> $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100> $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100> $lightness
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100> $alpha
     *
     * @return array{
     *     0: array{0: int<0, 360>, 1: int<0, 360>},
     *     1: array{0: int<0, 100>, 1: int<0, 100>},
     *     2: array{0: int<0, 100>, 1: int<0, 100>},
     *     3: array{0: int<0, 100>, 1: int<0, 100>}
     * }
     */
    public static function normalizeRange(array|int $hue, array|int $saturation, array|int $lightness, array|int $alpha): array
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

        if (is_int($alpha)) {
            $alpha = [$alpha, $alpha];
        }

        /* @phpstan-ignore smaller.alwaysFalse, greater.alwaysFalse, booleanOr.leftAlwaysFalse */
        if (count($hue) != 2 || $hue[0] > $hue[1] || $hue[1] > 360 || $hue[0] < 0) {
            throw new \UnexpectedValueException('The hue must be an array of the form [min, max] where 360 >= max >= min > 0');
        }

        /* @phpstan-ignore smaller.alwaysFalse, greater.alwaysFalse, booleanOr.leftAlwaysFalse */
        if (count($saturation) != 2 || $saturation[0] > $saturation[1] || $saturation[1] > 100 || $saturation[0] < 0) {
            throw new \UnexpectedValueException('The saturation must be an array of the form [min, max] where 100 >= max >= min > 0');
        }

        /* @phpstan-ignore smaller.alwaysFalse, greater.alwaysFalse, booleanOr.leftAlwaysFalse */
        if (count($lightness) != 2 || $lightness[0] > $lightness[1] || $lightness[1] > 100 || $lightness[0] < 0) {
            throw new \UnexpectedValueException('The lightness must be an array of the form [min, max] where 100 >= max >= min > 0');
        }

        /* @phpstan-ignore smaller.alwaysFalse, greater.alwaysFalse, booleanOr.leftAlwaysFalse */
        if (count($alpha) != 2 || $alpha[0] > $alpha[1] || $alpha[1] > 100 || $alpha[0] < 0) {
            throw new \UnexpectedValueException('The alpha must be an array of the form [min, max] where 100 >= max >= min > 0');
        }

        return [$hue, $saturation, $lightness, $alpha];
    }

    /**
     * @param array{0: int<0, 360>, 1: int<0, 360>}|int<0,360> $hue
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100> $saturation
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100> $lightness
     * @param array{0: int<0, 100>, 1: int<0, 100>}|int<0,100> $alpha
     */
    public static function boundedRandom(array|int $hue, array|int $saturation, array|int $lightness, array|int $alpha, ?string $seed = null): Hsla
    {
        [$hue, $saturation, $lightness, $alpha] = static::normalizeRange($hue, $saturation, $lightness, $alpha);

        return new self(
            Random::between($hue[0], $hue[1], $seed),
            Random::between($saturation[0], $saturation[1], $seed),
            Random::between($lightness[0], $lightness[1], $seed),
            Random::between($alpha[0], $alpha[1], $seed)
        );
    }

    public static function fromHex(string $hex): Hsla
    {
        if (!preg_match(self::HEX_REGEX, $hex, $matches)) {
            throw new \InvalidArgumentException("Invalid hex value `{$hex}` ");
        }

        if ($hex[0] === '#') {
            $hex = substr($hex, 1);
        }

        if (strlen($hex) === 5 || $hex !== $matches[1]) {
            throw new \InvalidArgumentException("Invalid hex value `{$hex}` ");
        }

        $a = 1;

        if (strlen($hex) === 8) {
            $a   = hexdec(substr($hex, 6, 2)) / 255;
            $hex = substr($hex, 0, 6);
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

        return static::fromRGBA($r, $g, $b, $a);
    }

    /**
     * @see {https://stackoverflow.com/a/39147465}
     */
    public static function fromRGBA(float|int $r, float|int $g, float|int $b, int|float $a): Hsla
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
            return new Hsla(0, 0, $lightness * 100, 1);
        }

        if ($channelMax === $r) {
            // PHP mod operator always returns an integer.
            $hue = fmod(($g - $b) / $delta, 6);
        } elseif ($channelMax === $g) {
            $hue = (($b - $r) / $delta) + 2;
        } elseif ($channelMax === $b) {
            $hue = (($r - $g) / $delta) + 4;
        }

        $hue = $hue * 60;

        if ($hue < 0) {
            $hue += 360;
        }

        $saturation = ($delta / (1 - abs(2 * $lightness - 1)));

        return new Hsla(
            $hue,
            $saturation * 100,
            $lightness * 100,
            $a
        );
    }

    /** @return int<0,255> Non-alpha-premultiplied red channel */
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

        /* @phpstan-ignore-next-line PHPStan can't see that the integers are bounded between 0-255, it's fine. */
        return [(int) round($f(0) * 255), (int) round($f(8) * 255), (int) round($f(4) * 255)];
    }

    /** @return string Returns the CSS representation of the equivalent color in RGB: rgb(r, g, b)   */
    public function toRgba(): string
    {
        return sprintf('rgba(%d, %d, %d, %f)', $this->red(), $this->green(), $this->blue(), $this->alpha / 100);
    }

    /** @return string Returns the CSS representation of the HSL color: hsl(h, s%, l%) */
    public function toHsla(): string
    {
        return sprintf(
            'hsl(%d %s %s / %g%%)',
            $this->hue,
            $this->saturation . '%',
            $this->lightness . '%',
            $this->alpha
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
            min(100, max(0, $this->lightness + $percentage)),
            $this->alpha
        );
    }

    /** @return string Returns the CSS representation of the HSL color: hsla(h, s%, l%, a) */
    public function __toString(): string
    {
        return $this->toHsla();
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
    public function contrast(Hsla $color): float
    {
        $original = $this->luminance();
        $against  = $color->luminance();

        $brightest = max($original, $against) + 0.05;
        $darkest   = min($original, $against) + 0.05;

        return $brightest / $darkest;
    }

    public function setHue(float|int $hue): self
    {
        // People can do the necessary angle conversion if needed, let's be dumb.
        if ($hue > 360 || $hue < 0) {
            throw new \InvalidArgumentException("Hue must be between 0 and 360, got: {$hue}");
        }

        $this->hue = $hue;

        return $this;
    }

    public function withLightness(float|int $lightness): Hsla
    {
        return new Hsla($this->hue, $this->saturation, $lightness, $this->alpha);
    }

    public function withSaturation(float|int $saturation): Hsla
    {
        return new Hsla($this->hue, $saturation, $this->lightness, $this->alpha);
    }

    public function withHue(int $hue): Hsla
    {
        return new Hsla($hue, $this->saturation, $this->lightness, $this->alpha);
    }

    public function withAlpha(float|int $alpha): Hsla
    {
        return new Hsla($this->hue, $this->saturation, $this->lightness, $alpha);
    }

    public function clone(): Hsla
    {
        return new Hsla($this->hue, $this->saturation, $this->lightness, $this->alpha);
    }

    public function setLightness(float|int $lightness): self
    {
        if ((int) $lightness > 100 || (int) $lightness < 0) {
            throw new \InvalidArgumentException("Lightness must be between 0 and 100, got: {$lightness}");
        }

        $this->lightness = $lightness;

        return $this;
    }

    public function setSaturation(float|int $saturation): self
    {
        if ((int) $saturation > 100 || (int) $saturation < 0) {
            throw new \InvalidArgumentException("Saturation must be between 0 and 100, got: {$saturation}");
        }

        $this->saturation = $saturation;

        return $this;
    }

    public function setAlpha(float|int $alpha): self
    {
        if ($alpha > 0 && $alpha <= 1) {
            $alpha *= 100;
        }

        if ((int) $alpha > 100 || (int) $alpha < 0) {
            throw new \InvalidArgumentException("Alpha must be between 0 and 100, got: {$alpha}");
        }

        $this->alpha = $alpha;

        return $this;
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
