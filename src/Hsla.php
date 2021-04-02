<?php

namespace Delight\Color;

use Delight\Color\Concerns\IsColor;
use Delight\Color\Contracts\Color;

class Hsla implements Color
{
    use IsColor;

    protected float $hue;
    protected float $saturation;
    protected float $lightness;
    protected float $alpha;

    public function __construct(float $hue, float $saturation, float $lightness, float $alpha)
    {
        $this->hue        = $hue;
        $this->saturation = $saturation;
        $this->lightness  = $lightness;
        $this->alpha      = $alpha;
    }

    public static function fromString(string $color): Hsla
    {
        [$h, $s, $l, $a] = explode(',', str_replace(['hsla(', ')', " \t\n\r\0\x0B", '%'], '', $color));

        return new self((float) $h, (float) $s, (float) $l, (float) $a);
    }

    public static function random(): Hsla
    {
        return new self(
            random_int(0, 360),
            random_int(0, 100),
            random_int(0, 100),
            round(mt_rand() / mt_getrandmax(), 1)
        );
    }

    public function red(): int
    {
        return $this->toRgb()->red();
    }

    public function toRgb(): Rgb
    {
        return $this->toHsl()->toRgb();
    }

    public function toHsl(): Hsl
    {
        return new Hsl(
            $this->hue,
            $this->saturation,
            $this->lightness
        );
    }

    public function alpha(): float
    {
        return $this->alpha;
    }

    public function hue(): float
    {
        return $this->hue;
    }

    public function saturation(): float
    {
        return $this->saturation;
    }

    public function lightness(): float
    {
        return $this->lightness;
    }

    public function blue(): int
    {
        return $this->toRgb()->blue();
    }

    public function green(): int
    {
        return $this->toRgb()->green();
    }

    public function toHex(): Hex
    {
        return $this->toRgb()->toHex();
    }

    public function toRgba(float $alpha = -1): Rgba
    {
        $rgb = $this->toRgb();

        return new Rgba(
            $rgb->red(),
            $rgb->green(),
            $rgb->blue(),
            $alpha == -1 ? $this->alpha : $alpha
        );
    }

    public function __toString(): string
    {
        return sprintf(
            'hsla(%s,%s,%s,%s)',
            $this->hue,
            $this->saturation . '%',
            $this->lightness . '%',
            $this->alpha
        );
    }

    public function toHsla(float $alpha = 1): Hsla
    {
        $this->alpha = $alpha;

        return $this;
    }
}
