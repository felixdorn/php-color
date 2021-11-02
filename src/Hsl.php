<?php

namespace Delight\Color;

class Hsl
{
    public float $hue;
    public float $saturation;
    public float $lightness;

    public function __construct(float $hue, float $saturation, float $lightness)
    {
        $this->hue        = $hue;
        $this->saturation = $saturation;
        $this->lightness  = $lightness;
    }

    public static function random(?string $seed = null): Hsl
    {
        return static::limitedRandom([0, 360], [0, 100], [0, 100], $seed);
    }

    public static function limitedRandom(array $hue, array $saturation, array $lightness, ?string $seed = null): Hsl
    {
        return new self(
            Random::between($hue[0], $hue[1], $seed),
            Random::between($saturation[0], $saturation[1], $seed),
            Random::between($lightness[0], $lightness[1], $seed),
        );
    }

    public static function fromString(string $color): Hsl
    {
        [$h, $s, $l] = explode(',', str_replace(['hsl(', ')', " \t\n\r\0\x0B", '%'], '', $color));

        return new self((int) $h, (int) $s, (int) $l);
    }

    public function red(): int
    {
        return $this->toRgb()[0];
    }

    private function toRgb(): array
    {
        $saturation = $this->saturation / 100;
        $lightness  = $this->lightness / 100;
        $c          = (1 - abs(2 * $lightness - 1)) * $saturation;
        $x          = $c * (1 - abs(fmod(($this->hue / 60), 2) - 1));
        $m          = $lightness - ($c / 2);

        if ($this->hue < 60) {
            $r = $c;
            $g = $x;
            $b = 0;
        } elseif ($this->hue < 120) {
            $r = $x;
            $g = $c;
            $b = 0;
        } elseif ($this->hue < 180) {
            $r = 0;
            $g = $c;
            $b = $x;
        } elseif ($this->hue < 240) {
            $r = 0;
            $g = $x;
            $b = $c;
        } elseif ($this->hue < 300) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }

        $r = ($r + $m) * 255;
        $g = ($g + $m) * 255;
        $b = ($b + $m) * 255;

        return [floor($r), floor($g), floor($b)];
    }

    public function isDark(): bool
    {
        return $this->lightness <= 15;
    }

    public function isBright(): bool
    {
        return $this->lightness >= 90;
    }

    public function darken(int $percentage): self
    {
        return new self(
            $this->hue,
            $this->saturation,
            $this->lightness - $percentage,
        );
    }

    public function lighten(int $percentage): self
    {
        return new self(
            $this->hue,
            $this->saturation,
            $this->lightness + $percentage
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
        $rgb = $this->toRgb();

        return dechex($rgb[0]) . dechex($rgb[1]) . dechex($rgb[2]);
    }

    public function blue(): int
    {
        return $this->toRgb()[2];
    }

    public function green(): int
    {
        return $this->toRgb()[1];
    }

    public function contrast(Hsl $color): float
    {
        $original = $this->luminance();
        $against  = $color->luminance();

        $brightest = max($original, $against) + 0.05;
        $darkest   = min($original, $against) + 0.05;

        return $brightest / $darkest;
    }

    public function luminance(): float
    {
        [$r, $g, $b] = array_map(function ($channel) {
            $channel /= 255;

            return $channel <= 0.03928 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4;
        }, $this->toRgb());

        return $r * 0.02126 + $g * 0.7152 + $b * 0.0722;
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
