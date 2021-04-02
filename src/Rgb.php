<?php

namespace Delight\Color;

use Delight\Color\Concerns\IsColor;
use Delight\Color\Contracts\Color;

class Rgb implements Color
{
    use IsColor;

    protected int $red;
    protected int $green;
    protected int $blue;

    public function __construct(int $red, int $green, int $blue)
    {
        $this->red   = $red;
        $this->green = $green;
        $this->blue  = $blue;
    }

    public static function fromString(string $color): Rgb
    {
        [$r, $g, $b] = explode(',', str_replace(['rgb(', ')', " \t\n\r\0\x0B"], '', $color));

        return new self((int) $r, (int) $g, (int) $b);
    }

    public static function random(): Rgb
    {
        return new self(
            random_int(0, 255),
            random_int(0, 255),
            random_int(0, 255)
        );
    }

    public function __toString(): string
    {
        return sprintf(
            'rgb(%s,%s,%s)',
            $this->red,
            $this->green,
            $this->blue
        );
    }

    public function red(): int
    {
        return $this->red;
    }

    public function green(): int
    {
        return $this->green;
    }

    public function blue(): int
    {
        return $this->blue;
    }

    public function toRgb(): Rgb
    {
        return $this;
    }

    public function toHex(): Hex
    {
        $convert = fn ($channel) => str_pad(dechex($channel), 2, '0', STR_PAD_LEFT);

        return new Hex(
            $convert($this->red),
            $convert($this->green),
            $convert($this->blue)
        );
    }

    public function toRgba(float $alpha = 1): Rgba
    {
        return new Rgba($this->red, $this->green, $this->blue, $alpha);
    }

    public function toHsla(float $alpha = 1): Hsla
    {
        $hsl = $this->toHsl();

        return new Hsla(
            $hsl->hue(),
            $hsl->saturation(),
            $hsl->lightness(),
            $alpha
        );
    }

    public function toHsl(): Hsl
    {
        $red   = $this->red / 255;
        $green = $this->green / 255;
        $blue  = $this->blue / 255;

        $mostPresent = max($red, $green, $blue);
        $lessPresent = min($red, $green, $blue);
        $delta       = $mostPresent - $lessPresent;
        $hue         = 0;
        $x           = 0;

        if ($delta !== 0) {
            switch ($mostPresent) {
                case $red:
                    $x = $green - $blue;
                    break;
                case $green:
                    $x = $blue - $red;
                    break;
                case $blue:
                    $x = $red - $green;
                    break;
            }

            $hue = 60 * fmod($x / $delta, 6);
        }

        $lightness = ($mostPresent + $lessPresent) / 2;

        if ($lightness === 0) {
            $saturation = 0;
        } else {
            $saturation = $delta / (1 - abs((2 * $lightness) - 1));
        }

        return new Hsl(round($hue), round(min($saturation, 1) * 100), round(min($lightness, 1) * 100));
    }
}
