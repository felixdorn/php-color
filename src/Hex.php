<?php

namespace Delight\Color;

use Delight\Color\Concerns\IsColor;
use Delight\Color\Contracts\Color;

class Hex implements Color
{
    use IsColor;

    protected string $hex;
    protected int $red;
    protected int $green;
    protected int $blue;

    public function __construct(string $red, string $green, string $blue)
    {
        $this->hex   = "{$red}{$green}{$blue}";
        $this->red   = (int) hexdec($red);
        $this->green = (int) hexdec($green);
        $this->blue  = (int) hexdec($blue);
    }

    public static function fromString(string $color): Hex
    {
        if (strpos($color, '#') === 0) {
            $color = substr($color, 1);
        }

        return new self(...str_split($color, 2));
    }

    public static function random(): Hex
    {
        return new self(
            dechex(random_int(0, 255)),
            dechex(random_int(0, 255)),
            dechex(random_int(0, 255))
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

    public function __toString(): string
    {
        return '#' . $this->hex;
    }

    public function toHex(): Hex
    {
        return $this;
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
        return $this->toRgb()->toHsl();
    }

    public function toRgb(): Rgb
    {
        return new Rgb($this->red, $this->green, $this->blue);
    }
}
